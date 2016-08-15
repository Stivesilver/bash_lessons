<?php

	Security::init();

	$date = io::post('date');
	$tsRefID = io::post('tsRefID');

	$age = array('date'=>'', 'age'=>'');
	if ($date != '') {
		$SQL = "
			SELECT age('" . $date . "'::date, stddob) AS date,
                   CASE WHEN ('" . $date . "'::date - stdevaldt::date) >  60 THEN ('" . $date . "'::date - stdevaldt::date) END AS age
              FROM webset.dmg_studentmst std
                   INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
             WHERE tsrefid = $tsRefID
        ";
		$age = db::execSQL($SQL)->assoc();
	}

	io::ajax('date', $age['date']);
	io::ajax('age', $age['age']);

?>
