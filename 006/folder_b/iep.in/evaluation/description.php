<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'ESY Recommendation';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_esy_recommend', 'stdrefid');

	$edit->addGroup('Extended School Year is recommended for');	
	$edit->addControl('Minutes per day', 'integer')->sqlField('rec_min');
	$edit->addControl('Days per week', 'integer')->sqlField('rec_day');
	$edit->addControl('Weeks', 'integer')->sqlField('rec_wek');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		
	$edit->finishURL = 'javascript:parent.switchTab(2);';
	$edit->cancelURL = 'javascript:parent.switchTab();';
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_esy_recommend')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>