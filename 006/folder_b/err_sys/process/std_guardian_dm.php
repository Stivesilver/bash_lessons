<?php
    $SQL = "
        SELECT COUNT(1)
          FROM webset.dmg_guardianmst grd
               INNER JOIN webset.sys_teacherstudentassignment ts ON ts.stdrefid = grd.stdrefid
         WHERE tsrefid = ".$tsRefID."
           AND COALESCE(gdeddecision, 'Y') = 'Y'
    ";

    if (db::execSQL($SQL)->getOne() == '0') {
        return true;
    } else {
        return false;
    }
?>