<?php

	Security::init();

	$dskey         = io::get('dskey');
	$ds            = DataStorage::factory($dskey, true);
	$stdSchoolYear = $ds->safeGet('stdIEPYear');
	$tsRefID       = $ds->safeGet('tsRefID');

	$SQL = "INSERT INTO webset_tx.std_sat_enroll (stdrefid, iepyear)
            SELECT $tsRefID, $stdSchoolYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_enroll
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdSchoolYear)";
	db::execSQL($SQL);

	$RefID = db::execSQL("
		SELECT erefid
		  FROM webset_tx.std_sat_enroll
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdSchoolYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_enroll', 'erefid');

	$edit->title = "Enrollment History";

	$edit->addGroup("General Information");
	$edit->addControl(FFSwitchYN::factory("Is this student currently enrolled in this district?"))->sqlField('curently');
	$edit->addControl("If no, explain:", "edit")->sqlField('curently_no')->size(70);
	$edit->addControl(FFSwitchYN::factory("Has the student recently transferred into the district?"))->sqlField('transfer');
	$edit->addControl("If yes, what is the transfer date?", "date")->sqlField('transfer_yes');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdSchoolYear)->sqlField('iepyear');
	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->saveAndEdit = true;
	$edit->firstCellWidth = "40%";

	$edit->printEdit();

?>