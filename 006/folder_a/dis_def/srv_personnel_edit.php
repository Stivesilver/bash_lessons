<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit Support For School Personnel';
    
    $edit->setSourceTable('webset.disdef_validvalues', 'refid');

    $edit->addGroup('General Information');
	
    $edit->addControl('Support For School Personnel', 'select')
        ->sqlField('validvalueid')
        ->name('validvalueid')
        ->sql("
			SELECT ssprefid,
				   sspdesc
			  FROM webset.statedef_services_supppersonnel 
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND ((CASE enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') 
			   AND COALESCE(nasw, 'N') != 'Y'
			 ORDER BY seqnum, sspdesc
        ")
        ->req();
	
    $edit->addControl('Default Narrative', 'textarea')
		->sqlField('validvalue')
		->css('width', '100%')
		->css('height', '100px');
	
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');  
	$edit->addControl('District ID', 'hidden')->name('vndrefid')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
	$edit->addControl('Value Name', 'hidden')->name('valuename')->value('MO_Personnel_Defaults')->sqlField('valuename');
    
    $edit->addSQLConstraint('Default Narrative for this Record already exists', 
        "
        SELECT 1 
          FROM webset.disdef_validvalues
         WHERE vndrefid = [vndrefid]
		   AND validvalueid = '[validvalueid]'
		   AND valuename = '[valuename]'
           AND refid != AF_REFID
    ");

    $edit->finishURL = 'srv_personnel_list.php';
    $edit->cancelURL = 'srv_personnel_list.php';
    
    $edit->firstCellWidth = '30%';

    $edit->printEdit();

?>
