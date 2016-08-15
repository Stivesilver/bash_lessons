<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Add/Edit Student Extracurricular and Non-Academic Activity';

	$edit->setSourceTable('webset.std_in_ena_activities', 'stdrefid');
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->addGroup('General Information');
	$edit->addControl(
		FFSwitchYN::factory($set_ini["in_extracurricular_question"])
			->sqlField('participate_sw')
	);
	
	$edit->addControl('Narrative', 'textarea')
		->sqlField('sieanarrtext')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_ena_activities')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>