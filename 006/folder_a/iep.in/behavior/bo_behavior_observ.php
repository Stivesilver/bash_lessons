<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Edit ' . $set_ini["in_behavioral_observations"];
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset.std_in_behavior', 'stdrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Strengths', 'textarea')
		->sqlField('bosdesc')
		->value(IDEACore::disParam(124))
		->css('width', '100%')
		->css('height', '150px');

	$edit->addControl('Concerns', 'textarea')
		->sqlField('bowdesc')
		->value(IDEACore::disParam(124))
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_behavior')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
