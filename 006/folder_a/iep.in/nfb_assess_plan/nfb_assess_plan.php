<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Need for Functional Behavioral Assessment and Plan';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset.std_nfb_assess_plan', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Select appropriate', 'select_radio')
		->sqlField('naprefid')
		->sql("
			SELECT naprefid,
			       naptext
			  FROM webset.disdef_nfb_assess_plan
		     WHERE vndrefid = VNDREFID
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 ORDER BY naprefid
		")
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_nfb_assess_plan')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>