<?php

	Security::init();
	IDEAFormat::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_id = IDEAFormat::get('id');

	$bNames = IDEAFormat::getDocBlocks();

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit ' . $bNames[7]['iepdesc'];

	$edit->setSourceTable('webset.std_in_supp_inf', 'sirefid');

	if (io::get('RefID') == 0) {
		$sql = "
			SELECT sicrefid, 
			       sicdesc
			  FROM webset.statedef_supp_inf_cat
			 WHERE screfid = " . VNDState::factory()->id . "
               AND sicrefid NOT IN (
							SELECT sicrefid 
							  FROM webset.std_in_supp_inf 
							 WHERE stdrefid = " . $tsRefID . "
			   )
	   	     ORDER BY sicseq, sicrefid
		";
	} else {
		$sql = "
			SELECT sicrefid, 
			       sicdesc
			  FROM webset.statedef_supp_inf_cat
			 WHERE screfid = " . VNDState::factory()->id . "
               AND sicrefid IN (
					SELECT sicrefid 
					  FROM webset.std_in_supp_inf 
					 WHERE sirefid = " . io::get('RefID') . "
			   )
	   	     ORDER BY sicseq, sicrefid			
		";
	}
	
	$edit->addGroup('General Information');
	$edit->addControl('Category', 'select')
		->sqlField('sicrefid')
		->sql($sql)
		->req();

	$edit->addControl('Narrative', 'textarea')
		->sqlField('sinarr')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('si_suppinfo.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('si_suppinfo.php', array('dskey' => $dskey));
	
	$edit->saveAndAdd = db::execSQL($sql)->recordCount() > 1;
	
	$edit->printEdit();
?>