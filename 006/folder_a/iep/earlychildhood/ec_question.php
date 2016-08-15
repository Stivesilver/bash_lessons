<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsRefID');

	$edit->title = 'Early Childhood Question';

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->addGroup('General Information');
	$edit->addControl(FFSwitchYN::factory('Early Childhood Student'))
		->value('N')
		->sqlField('stdearlychildhoodsw');

	$edit->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.sys_teacherstudentassignment')
		->setKeyField('tsrefid')
		->applyEditClassMode()
	);

	$edit->printEdit();
?>
