<?php
	Security::init();

    $RefID = io::geti('RefID');
    
    $edit = new EditClass('edit1', $RefID);
    $edit->title = 'Add/Edit Guardian'; 
    
    if ($RefID==0) {
	    $studentData = db::execSQL("
                           SELECT stdlnm,
	                              splrefid,
	                              stdhadr1,
	                              stdhadr2,
	                              stdhcity,
	                              stdhstate,
	                              stdhzip,
	                              stdhphn
                             FROM webset.dmg_studentmst
                            WHERE stdrefid = " . io::get('stdrefid') ."
                       ");                                             
	}    
    
	$edit->setSourceTable('webset.dmg_guardianmst', 'gdrefid');

	$edit->addTab('General Information');
	$edit->addControl('First Name')->sqlField('gdfnm')->size(30)->req();
	$edit->addControl('Last Name')->sqlField('gdlnm')->size(30)->req()->value($RefID==0?$studentData->fields['stdlnm']:'');
	$edit->addControl('Type', 'select')
		->sqlField('gdtype')
		->sql("
            SELECT gtrefid,
                   gtdesc
              FROM webset.def_guardiantype
             ORDER BY gtrank
        ");
	$edit->addControl(FFSwitchYN::factory('Decision Maker'))->sqlField('gdeddecision');
	$edit->addControl('Order#', 'integer')->sqlField('seqnumber')->size(10);
	$edit->addControl('Language of Parent: ', 'select')
		->value(SystemCore::$DBUtils->execSQL(
                    "
                    SELECT refid
                      FROM webset.statedef_prim_lang
                     WHERE screfid = " . VNDState::factory()->id . "
                       AND LOWER(adesc) = LOWER('English')
                       AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                ")->getOne()
        )
		->sqlField('ghlrefid')
        ->emptyOption(true)  
        ->sql("
            SELECT refid,
                   adesc
              FROM webset.statedef_prim_lang
             WHERE screfid = " . VNDState::factory()->id . "             
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
             ORDER BY 2
        ")
        ->value($RefID==0?$studentData->fields['splrefid']:'');
    
	$edit->addTab('Contact Information');
	$edit->addControl('Address 1')->sqlField('gdadr1')->size(40)->value($RefID==0?$studentData->fields['stdhadr1']:'');
	$edit->addControl('Address 2')->sqlField('gdadr2')->size(40)->value($RefID==0?$studentData->fields['stdhadr2']:'');
	$edit->addControl('City: ')->sqlField('gdcity')->value($RefID==0?$studentData->fields['stdhcity']:'');
	$edit->addControl('State: ')->sqlField('gdstate')->size(2)->value($RefID==0?$studentData->fields['stdhstate']:'');
	$edit->addControl('Zip Code', 'integer')->sqlField('gdcitycode')->size(10)->value($RefID==0?$studentData->fields['stdhzip']:'');
	$edit->addControl('Home Phone', 'phone')->sqlField('gdhphn')->value($RefID==0?$studentData->fields['stdhphn']:'');
	$edit->addControl('Mobile Phone', 'phone')->sqlField('gdmphn');	                                       
	$edit->addControl('Work Phone', 'phone')->sqlField('gdwphn');
	$edit->addControl('Work Phone Ext')->sqlField('gdwphn_ext')->size(4);
	$edit->addControl('Work Name')->sqlField('gdwplace')->size(40);
	$edit->addControl('Email Address')->sqlField('gdemail')->size(40);
    
	$edit->addControl('Comment', 'textarea')
		->sqlField('gdcmt')
		->css('WIDTH', '100%')
		->css('HEIGHT', '30px');
        
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('stdrefid', 'hidden')->value(io::get('stdrefid'))->sqlField('stdrefid');
    //$edit->topButtons = flase;
    $edit->firstCellWidth = '40%';
    $edit->finishURL = CoreUtils::getURL('guard_list.php', array('stdrefid'=>io::get('stdrefid')));
    $edit->cancelURL = CoreUtils::getURL('guard_list.php', array('stdrefid'=>io::get('stdrefid')));

    $edit->printEdit();

?>
