<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);
	$values     = IDEADef::getValidValues('TX_IEP_Updates');
	$checkboxes = array();

	$edit->title       = 'ARD/IEP Updates';
	$edit->finishURL   = '';
	$edit->topButtons  = true;
	$edit->saveAndAdd  = false;
	$edit->saveLocal   = false;
	$edit->saveAndEdit = true;

	foreach ($values as $val) {
		$key              = $val->get(IDEADefValidValue::F_REFID);
		$checkboxes[$key] = '<b>' . $val->get(IDEADefValidValue::F_VALUE_ID) .
							'</b> <i>' . $val->get(IDEADefValidValue::F_VALUE) . '</i>';
	}

	$edit->setPresaveCallback('updateIEP', 'update_iep.inc.php');
	$edit->addGroup('General Information');
	$edit->addControl('Address and include only the applicable attachments', 'select_check')
		->data($checkboxes)
		->breakRow()
		->name('selected')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_ard', 'iep_updates', $stdIEPYear));

	$edit->addControl('tsRefID', 'hidden')
		->name('tsRefID')
		->value($tsRefID);

	$edit->addControl('stdIEPYear', 'hidden')
		->name('stdIEPYear')
		->value($stdIEPYear);

	$edit->printEdit();

?>