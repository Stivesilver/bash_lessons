<?php

    $archived = false;

    $date_start = $student->getDate('stdiepyearbgdt');
    $date_end = $student->getDate('stdiepyearendt');

    if ($student->getDate('stdiepmeetingdt')) $date_start = $student->getDate('stdiepmeetingdt');
    if ($student->getDate('stdcmpltdt')) $date_end = $student->getDate('stdcmpltdt');

    if ($date_start == '') return;

    $year = substr($date_start, 0, 4);
    $month = substr($date_start, 5, 2);
    $day = substr($date_start, 8, 2);
    $startd = mktime(0, 0, 0, $month, $day, $year);
    $year = substr($date_end, 0, 4);
    $month = substr($date_end, 5, 2);
    $day = substr($date_end, 8, 2);
    $endd = mktime(0, 0, 0, $month, $day, $year);
    $where = "AND siepmdocdate >= '$date_start'";
    $years = substr($date_start, 0, 4) . "-" . substr($date_end, 0, 4) . " ";

    $SQL = "
        SELECT stdiepmeetingdt
	      FROM webset.std_iep
	     WHERE stdrefid = " . $tsRefID . "
	       AND COALESCE(iep_status, 'A') != 'I'
    ";
    $result = db::execSQL($SQL);

    while (!$result->EOF) {
        $from_iep = $result->fields[0];
        $year = substr($from_iep, 0, 4);
        $month = substr($from_iep, 5, 2);
        $day = substr($from_iep, 8, 2);
        $iepdate = mktime(0, 0, 0, $month, $day, $year);

        if ($startd <= $iepdate and $iepdate <= $endd) {
            $archived = true;
            break;
        }
        $result->MoveNext();
    }

    if (!$archived) {
        $SQL = "
            SELECT 1
	          FROM webset.std_iep
	         WHERE stdrefid = " . $tsRefID . "
	           AND COALESCE(iep_status, 'A') != 'I'
	         " . $where . "
        ";
        $result = db::execSQL($SQL);
        if ($result->fields[0] == 1) {
            $archived = true;
        }
    }

    if ($archived) {
        return array('menutext' => 'IEP Builder (' . $years . 'IEP archived)', 'condition' => true);
    } else {
        return array('menutext' => 'IEP Builder (' . $years . 'IEP not archived)', 'condition' => true);
    }
?>