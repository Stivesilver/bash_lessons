<?php
    Security::init();

	$dskey   = io::get('dskey');
	$tsRefID = DataStorage::factory(io::get('dskey'))->safeGet('tsRefID');
	$set_ini = IDEAFormat::getIniOptions();
		
	$edit = new EditClass("edit1", io::geti('RefID'));
	
	$edit->setSourceTable('webset.std_disabilitymst', 'sdrefid');

	$edit->title = 'Add/Edit '.$set_ini['disability_title'];

	$edit->addGroup('General Information');
	$edit->addControl($set_ini['disability_title'], 'select_radio')
		->sqlField('dcrefid')
		->name('dcrefid')
		->sql("
            SELECT dcrefid,
                   COALESCE (dccode || ' - ', '') || dcdesc
		      FROM webset.statedef_disablingcondition
		     WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
             ORDER BY dccode
        ")
        ->breakRow()
        ->req();
		
	$edit->addControl('Type', 'select_radio')
		->sqlField('sdtype')
		->sql("
            SELECT validvalueid, 
		           validvalue
			  FROM webset.glb_validvalues
		     WHERE valuename = 'DisabilityType'
             ORDER BY validvalueid
        ")
        ->value(
            db::execSQL("
                SELECT 1 
                  FROM webset.std_disabilitymst 
                 WHERE sdtype = 1 
                   AND stdrefid = ".$tsRefID."
            ")->getOne() == 1 ? '2' : '1'
        )
        ->breakRow();
	
	$edit->addControl('Areas of Qualification', 'select_check')
		->sqlField('indicators')
		->name('indicators')
		->sql("
			SELECT adirefid,
				   ad.code || '. ' || adname
			  FROM webset.statedef_disabling_indicatam as am
				   INNER JOIN webset.statedef_disabling_indicatad as ad ON am.amirefid = ad.amirefid
			 WHERE dis_id = VALUE_01
			 ORDER BY am.code, ad.code
        ")
		->showIf(
			'dcrefid', 
			db::execSQL("
				SELECT dis_id
				  FROM webset.statedef_disabling_indicatam
			")->indexAll()
		)
        ->breakRow()        
        ->tie('dcrefid')
		;
	
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');		
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');		
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	
	$edit->addSQLConstraint('You are trying to add duplicate Disability', 
	    "
        SELECT 1 
	      FROM webset.std_disabilitymst
	     WHERE stdrefid = " . $tsRefID . "
	       AND dcrefid = [dcrefid]
	       AND sdrefid != AF_REFID
    ");
	        
	$edit->finishURL = CoreUtils::getURL('cd_dis_cat.php', array('dskey'=>$dskey));
	$edit->cancelURL = CoreUtils::getURL('cd_dis_cat.php', array('dskey'=>$dskey));

	$edit->printEdit();

?>