<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_sat_strength (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_strength
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear)
        ";
	db::execSQL($SQL);

	$RefID = db::execSQL(
		"SELECT srefid
		   FROM webset_tx.std_sat_strength
          WHERE stdrefid = $tsRefID
            AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_strength', 'srefid');

	$edit->title       = "Social Skills/Behavior";
	$edit->saveAndEdit = true;

	$edit->addGroup("General Information");
	$edit->addControl("Peer interactions", "select_radio")
		->sqlField('sk_peer')
		->sql("SELECT 'E', 'Excellent', 1 UNION SELECT 'S', 'Satisfactory', 2  UNION SELECT 'U', 'Unsatisfactory', 2 ORDER BY 3");

	$edit->addControl("Follows instructions", "select_radio")
		->sqlField('sk_foll')
		->sql("SELECT 'E', 'Excellent', 1 UNION SELECT 'S', 'Satisfactory', 2  UNION SELECT 'U', 'Unsatisfactory', 2 ORDER BY 3");

	$edit->addControl("Stays on task", "select_radio")
		->sqlField('sk_stay')
		->sql("SELECT 'E', 'Excellent', 1 UNION SELECT 'S', 'Satisfactory', 2  UNION SELECT 'U', 'Unsatisfactory', 2 ORDER BY 3");

	$edit->addControl("Teacher interactions", "select_radio")
		->sqlField('sk_inte')
		->sql("SELECT 'E', 'Excellent', 1 UNION SELECT 'S', 'Satisfactory', 2  UNION SELECT 'U', 'Unsatisfactory', 2 ORDER BY 3");

	$edit->addControl("Other", "edit")->sqlField('sk_other')->size(30);

	$edit->addControl("Other", "select_radio")
		->sqlField('sk_othe')
		->sql("SELECT 'E', 'Excellent', 1 UNION SELECT 'S', 'Satisfactory', 2  UNION SELECT 'U', 'Unsatisfactory', 2 ORDER BY 3");

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>