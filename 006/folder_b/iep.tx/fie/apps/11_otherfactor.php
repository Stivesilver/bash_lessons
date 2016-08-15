<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$edit       = new EditClass('edit1', $tsRefID);

	$edit->setSourceTable('webset_tx.std_fie_general', 'stdrefid');

	$edit->SQL = "
		SELECT '',
	           factors_oth_ch,
	           factors_oth
		  FROM webset_tx.std_fie_general
		 WHERE stdrefid=$tsRefID
		   AND iepyear=$stdIEPYear
	 	";

	$edit->title       = "Other Factors to Consider to Ensure FAPE";
	$edit->saveLocal   = false;
	$edit->saveAndEdit = true;
	$edit->finishURL   = true;

	$edit->addGroup("General Information");

	$edit->addObject(
		UICustomHTML::factory('
			Based upon this information, it was decided that assistive technology devices
			and/or services are required in order for the student to receive a free appropriate public education.
			If yes, please explain
		')
		->css('padding-left', '20%')
	);

	$edit->addControl(FFSwitchYN::factory(""))
		->name('SW')
		->sqlField('factors_oth_ch');

	$edit->addControl("", "textarea")
		->sqlField('factors_oth')
		->css("width", "100%")
		->css("height", "100px")
		->showIf('SW', 'Y');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL  = 'javascript:parent.selectNext()';
	$edit->saveAndAdd = false;

	$edit->printEdit();

?>