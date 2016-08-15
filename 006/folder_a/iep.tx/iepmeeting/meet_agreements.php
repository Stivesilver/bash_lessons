<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$area = io::get('area');

	$edit = new EditClass("edit1", $stdIEPYear);

	$stdinfo = IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'signatures_agreements_' . $area, $stdIEPYear);
	preg_match("/field0\|(.+?)!!!/", $stdinfo, $field0);
	preg_match("/field1\|(.+?)!!!/", $stdinfo, $field1);
	preg_match("/field2\|(.+?)!!!/", $stdinfo, $field2);
	preg_match("/field3\|(.+?)!!!/", $stdinfo, $field3);
	preg_match("/field4\|(.+?)!!!/", $stdinfo, $field4);

	$edit->title = 'Agreement';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '30%';

	$edit->addGroup('General Information');

	$edit->addControl('Agreement' , 'select_radio')
			->name('field0')
			->value(isset($field0[1]) ? $field0[1] : '')
			->data(
				array(
					'Y' => 'The committee mutually agreed to implement the services reflected in these proceedings',
					'N' => 'The members of this ARD committee have not reached mutual agreement'
				)
			)
		->breakRow();

	$edit->addControl('The committee will reconvene on this date' , 'date')
		->name('field1')
		->value(isset($field1[1]) ? $field1[1] : '')
		->showIf('field0', 'N');

	$edit->addControl('At this location')
		->name('field2')
		->value(isset($field2[1]) ? $field2[1] : '')
		->showIf('field0', 'N')
		->size(90);

	$edit->addControl('', 'select_check')
		->name('field3')
		->value(isset($field3[1]) ? $field3[1] : '')
		->data(array('Y' => 'Information explaining why mutual agreement has not been reached shall be attached'))
		->displaySelectAllButton(FALSE);

	$edit->addControl('', 'select_check')
		->name('field4')
		->value(isset($field4[1]) ? $field4[1] : '')
		->data(array('Y' => 'The members who disagree shall be offered the opportunity to write their own statement.'))
		->displaySelectAllButton(FALSE);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL('meet_agreements_save.php', array('dskey' => $dskey, 'area' => $area));
	$edit->saveURL = CoreUtils::getURL('meet_agreements_save.php', array('dskey' => $dskey, 'area' => $area));
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
