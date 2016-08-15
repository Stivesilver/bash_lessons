<?php
    $data = db::execSQL("
        SELECT TO_CHAR(ts.stdenrolldt, 'MM/DD/YYYY') as stdenrolldt,
               TO_CHAR(ts.stdiepmeetingdt, 'MM/DD/YYYY') as stdiepmeetingdt,
               TO_CHAR(ts.stdcmpltdt, 'MM/DD/YYYY') as stdcmpltdt,
               TO_CHAR(ts.stdevaldt, 'MM/DD/YYYY') as stdevaldt,
               TO_CHAR(ts.stdtriennialdt, 'MM/DD/YYYY') as stdtriennialdt,
               TO_CHAR(stdenterdt, 'MM/DD/YYYY') as stdenterdt,
               TO_CHAR(stdexitdt, 'MM/DD/YYYY') as stdexitdt
          FROM webset.sys_teacherstudentassignment ts
         WHERE tsrefid = " . $defaults['tsRefID'] . "
    ")->assoc();

	$fvalues = '
		<values>
			<value name="stdenrolldt">' . $data['stdenrolldt'] . '</value>
			<value name="stdiepmeetingdt">' . $data['stdiepmeetingdt'] . '</value>
			<value name="stdcmpltdt">' . $data['stdcmpltdt'] . '</value>
			<value name="stdevaldt">' . $data['stdevaldt'] . '</value>
			<value name="stdtriennialdt">' . $data['stdtriennialdt'] . '</value>
			<value name="stdenterdt">' . $data['stdenterdt'] . '</value>
			<value name="stdexitdt">' . $data['stdexitdt'] . '</value>
		</values>
	';
?>