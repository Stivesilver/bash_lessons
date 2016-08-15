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

	$edit->title = "Attendance History";

	$edit->addGroup("General Information");
	$edit->addControl("This student has been absent", "edit")->sqlField('absent_days')->size(4);
	$edit->addControl("Out of school days", "edit")->sqlField('school_days')->size(4);
	$edit->addControl("Number of excused absences", "edit")->sqlField('abs_good')->size(4);
	$edit->addControl("Number of unexcused absences", "edit")->sqlField('abs_bad')->size(4);
	$edit->addControl("Reasons for absences", "list")
		->sqlField('reason_abs')
		->data(
			array(
				'I' => 'Illness',
				'S' => 'Skipping classes',
				'T' => 'Truant'
			)
		);


	$edit->addControl("If illness Number of days", "edit")->sqlField('ill_days')->size(4);

	$edit->addControl("If illness Notes from parents", "edit")->sqlField('ill_recs')->size(4);

	$edit->addControl("Skipping classes", "edit")->sqlField('skiping_classes')->size(70);

	$edit->addControl(FFSwitchYN::factory("Has a truancy report been filed?"))->sqlField('truancy');

	$edit->addControl("If yes, date of report", "date")->sqlField('truancy_yes');

	$edit->addControl(FFSwitchYN::factory("Can this student's academic/achievement difficulties be attributed to lack of attendance?"))->sqlField('attlack');

	$edit->addControl(FFSwitchYN::factory("Have there been attendance problems in previous school years?"))->sqlField('attproblem');

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdSchoolYear)->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "40%";

	$edit->printEdit();
?>