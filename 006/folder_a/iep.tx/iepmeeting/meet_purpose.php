<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = IDEAStudentTX::factory($tsRefID);

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Purpose of Meeting';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset_tx.std_meet_purpose', 'iepyear');

	$edit->addGroup('Purpose');
	$edit->addControl('Purpose of Meeting', 'select_check')
		->sqlField('type_report')
		->sql("
			SELECT refid, adesc
			  FROM webset_tx.def_meetpurpose
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum, adesc
		")
		->breakRow();

	$edit->addGroup('IEP Type');
	$edit->addControl('Purpose of Meeting', 'select_check')
		->sqlField('type_iep')
		->sql("
			SELECT siepmtrefid,
				   siepmtdesc
			  FROM webset.statedef_ieptypes
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY CASE siepmtdesc WHEN 'Other' THEN 'zzzz' else siepmtdesc END
		")
		->breakRow();

	$edit->addControl('If Other, specify')
		->sqlField('type_iep_other')
		->size(70);

	$edit->addControl(
		FFSwitchYN::factory('AAllow blank ARD/IEP Dates')
			->sqlField('allowblankdates')
			->emptyOption(TRUE)
			->breakRow()
	);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_meet_purpose')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>