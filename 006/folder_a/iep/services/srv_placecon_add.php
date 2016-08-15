<?php
    Security::init();
    
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
    $RefID     = io::geti('RefID');

    $edit = new EditClass("edit1", io::geti('RefID'));
    
    $edit->setSourceTable('webset.std_placecon', 'sscmrefid');
    
    $edit->title = 'Add Placement Considerations';

    $edit->addGroup("General Information");
    $SQL = $RefID > 0 ? "
                        SELECT scmrefid, 
                               scmsdesc
                          FROM webset.statedef_placecon_quest
                         WHERE scmrefid IN (SELECT scqrefid
                                              FROM webset.std_placecon
                                             WHERE sscmrefid = ".$RefID.")
                         ORDER BY scmsdesc
                     " : "
                        SELECT scmrefid, scmsdesc
                          FROM webset.statedef_placecon_quest
                         WHERE screfid = ".VNDState::factory()->id."
                           AND scmlinksw = 'N'
                           AND scmrefid NOT IN (SELECT scqrefid
                                                  FROM webset.std_placecon
                                                 WHERE stdrefid = ".$tsRefID."
                                                   AND scarefid IS NOT NULL)                           
                         ORDER BY scmsdesc
                     ";
                     
    $edit->addControl("Not Completed Placement Considerations", "select")
        ->sqlField('scqrefid')
        ->name('scqrefid')
        ->sql($SQL)
        ->req();
    
    $edit->addControl('Question', 'textarea')
        ->sql("
            SELECT regexp_replace(scmquestion, '<[^>]*>', '', 'g') as scmquestion
              FROM webset.statedef_placecon_quest
             WHERE scmrefid = VALUE_01 
        ")
        ->disabled(true)
        ->css("width", "100%")
        ->tie('scqrefid'); 
        
    $edit->addControl("Answer", "select")
        ->sqlField('scarefid')
        ->name('scarefid')
        ->sql("
            SELECT scarefid,
                   regexp_replace(scanswer, '<[^>]*>', '', 'g') as scanswer
              FROM webset.statedef_placecon_answ
             WHERE scmrefid = VALUE_01
             ORDER BY CASE UPPER(SUBSTR(scanswer, 1, 2)) WHEN 'NO' THEN 1 WHEN 'YE' THEN 2 ELSE 3 END, scanswer
        ")
         ->tie('scqrefid')
         ->req(); 
    
	$narrative = $edit->addControl('Narrative', 'textarea')
		->sqlField('sscmnarrative')
		->css("width", "100%");

	if ($RefID == 0) {
		$narrative->SQL("
            SELECT scnarrativedefault
              FROM webset.statedef_placecon_answ
             WHERE scarefid = VALUE_01
		")->tie('scarefid');
	}
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
            
    $edit->finishURL = CoreUtils::getURL('srv_placecon.php', array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL('srv_placecon.php', array('dskey'=>$dskey));
    
    $edit->firstCellWidth  = '30%';
    
    $edit->saveAndAdd = (db::execSQL($SQL.' OFFSET 1 ')->getOne()!='');
        
    $edit->printEdit();

?>
