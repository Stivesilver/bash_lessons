<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Related Services Dates';

	$edit->setSourceTable('webset_tx.std_notes', 'siairefid');

	$edit->addGroup('General Information');
	$edit->addControl('Text', 'textarea')
		->sqlField('notes')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('notes.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('notes.php', array('dskey' => $dskey));

	$edit->printEdit();
?>