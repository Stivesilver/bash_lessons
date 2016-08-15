<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "INSERT INTO webset_tx.std_sat_behavior (stdrefid, iepyear)
            SELECT $tsRefID, $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_behavior
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear)
           ";

	$result = db::execSQL($SQL);
	$RefID = db::execSQL("
		SELECT brefid
		  FROM webset_tx.std_sat_behavior
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_behavior', 'brefid');

	$edit->title       = "Behavior";
	$edit->saveAndEdit = true;

	$edit->addGroup("General Information");
	$edit->addControl("Describe the behavior (and location) for which the teacher is seeking guidance:", "textarea")
		->sqlField('guidance')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("Describe the behavior management techniques used in the
                       classroom and the corresponding response of the student:", "textarea")
		->sqlField('techniques')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("Which behavior management technique has been the most effective:", "textarea")
		->sqlField('mosteffective')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("Which behavior management technique has been the least effective:", "textarea")
		->sqlField('leasteffective')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->printEdit();

?>