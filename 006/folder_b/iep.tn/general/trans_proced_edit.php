<?php

	Security::init();

	$RefID = io::get('RefID');

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit', $RefID);

	$edit->title = 'Add/Edit Transitioning Procedures';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('Planned Transitioning Procedures', 'textarea')
		->sqlField('txt01');

	$edit->addControl('Implementor')
		->sqlField('txt02')
		->css('width', '100%');

	$edit->addControl('Timeframe')
		->sqlField('txt03')
		->css('width', '100%');

	$edit->addControl('Date Completed', 'date')
		->sqlField('dat01');

	$edit->addUpdateInformation();

	$edit->addControl('Student ID', 'hidden')
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl('IEP Year ID', 'hidden')
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addControl('area_id', 'hidden')
		->sqlField('area_id')
		->value(IDEAAppArea::TN_IFSP_TRANSITION_FORMC);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
