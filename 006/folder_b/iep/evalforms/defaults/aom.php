<?php
    $SQL = "
        SELECT TO_CHAR(stdDOB + interval '18 year', 'MM/DD/YYYY') as aom_date
          FROM webset.sys_teacherstudentassignment t0
               INNER JOIN webset.dmg_studentmst t1 ON t0.stdrefid = t1.stdrefid
         WHERE tsrefid = " . $defaults['tsRefID'] ."
    ";
    $value = DB::execSQL($SQL)->getOne();
	$fvalues = '
		<values>
			<value name="CurrentDate">' . $value . '</value>
		</values>
	';
?>