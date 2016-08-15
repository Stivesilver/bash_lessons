<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Reasons';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->addGroup('General Information');

	if ($mode == 'S') {
		$edit->addControl('If efforts are not successful, give reasons:', 'textarea')
			->name('reasons')
			->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'lre_effort_not_success', $stdIEPYear))
			->css('width', '100%')
			->css('height', '150px');
	} else {
		$edit->addControl('If options were discussed and rejected, give reasons:', 'textarea')
			->name('rejected')
			->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'lre_options_rejected', $stdIEPYear))
			->css('width', '100%')
			->css('height', '150px');
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID);
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'));

	$edit->finishURL = 'javascript:parent.switchTab();';
	$edit->cancelURL = 'javascript:parent.switchTab();';
	$edit->saveURL = CoreUtils::getURL('reasons_save.php', array('dskey' => $dskey, 'mode' => $mode));

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>