<?php
	Security::init();

    $RefID = io::geti('RefID');
    
    $edit = new EditClass('edit1', $RefID);
    $edit->title = 'Add/Edit Emergency Contact'; 
  
	$edit->setSourceTable('c_manager.ec_contact', 'ec_refid');
                         
	$edit->addTab('General Information');
	$edit->addControl('First Name')->sqlField('ec_fname')->size(30)->req();
	$edit->addControl('Last Name')->sqlField('ec_lname')->size(30)->req();
	$edit->addControl('Relationship', 'select')
		->sqlField('gtrefid')
		->sql("
            SELECT gtrefid,
                   gtdesc
              FROM webset.def_guardiantype
             ORDER BY gtrank
        ");
                
	$edit->addControl('Primary Language: ', 'select')
		->value(SystemCore::$DBUtils->execSQL(
                    "
                    SELECT refid
                      FROM webset.statedef_prim_lang
                     WHERE screfid = " . VNDState::factory()->id . "
                       AND LOWER(adesc) = LOWER('English')
                       AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                ")->getOne()
        )
		->sqlField('ec_primary_language_refid')
        ->emptyOption(true)  
        ->sql("
            SELECT refid,
                   adesc
              FROM webset.statedef_prim_lang
             WHERE screfid = " . VNDState::factory()->id . "             
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
             ORDER BY 2
        ");
    
	$edit->addTab('Contact Information');
	$edit->addControl('Address 1')->sqlField('ec_addr1')->size(40);
	$edit->addControl('Address 2')->sqlField('ec_addr2')->size(40);
	$edit->addControl('City: ')->sqlField('ec_city');
	$edit->addControl('State: ')->sqlField('ec_state');
	$edit->addControl('Zip Code', 'integer')->sqlField('ec_zip');
	$edit->addControl('Home Phone', 'phone')->sqlField('ec_hphone');
	$edit->addControl('Mobile Phone', 'phone')->sqlField('ec_mphone');	                                       
	$edit->addControl('Work Phone', 'phone')->sqlField('ec_wphone');
	$edit->addControl('Work Phone Ext')->sqlField('ec_wphone_ext')->size(4);
	$edit->addControl('Work Name')->sqlField('ec_workname')->size(40);
	$edit->addControl('Email Address')->sqlField('ec_email')->size(40);
         
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('stdrefid', 'hidden')->value(io::get('stdrefid'))->sqlField('stdrefid');
    //$edit->topButtons = flase;
    $edit->firstCellWidth = '40%';
    $edit->finishURL = CoreUtils::getURL('emer_list.php', array('stdrefid'=>io::get('stdrefid')));
    $edit->cancelURL = CoreUtils::getURL('emer_list.php', array('stdrefid'=>io::get('stdrefid')));

    $edit->printEdit();

?>
