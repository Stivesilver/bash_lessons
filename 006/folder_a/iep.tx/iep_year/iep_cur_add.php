<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = 'Select Student Folder';

	$edit->setSourceTable('webset.std_iep_year', 'siymrefid');

	$edit->getButton(EditClassButton::SAVE_AND_ADD)->value('');
	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Save Folder');

	$edit->addGroup('General Information');
	$edit->addControl('Title', 'protected')
		->sqlField('ieptitle');

	$edit->addControl('Anticipated IEP Initiation Date', 'protected')
		->sqlField('siymiepbegdate');

	$edit->addControl('Anticipated IEP Annual Review Date', 'protected')
		->sqlField('siymiependdate');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	//    $edit->finishURL = CoreUtils::getURL('iep_cur_save.php', array('dskey' => $dskey));
	//    $edit->saveURL = CoreUtils::getURL('iep_cur_save.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('iep_cur.php', array('dskey' => $dskey));

	$edit->setPresaveCallback('updateIEP', 'iep_update.inc.php');

	$edit->onSaveDone = $ds->safeGet('refresh_screen_js');
	$edit->saveLocal = false;
	$edit->firstCellWidth = '40%';

	$edit->printEdit();
?>
