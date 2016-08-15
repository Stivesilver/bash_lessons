<?php
    $SQL = "
        SELECT DATE_PART('days', stdtriennialdt - NOW())
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = ".$tsRefID."
    ";

    if (db::execSQL($SQL)->getOne() < 0) {
        return true;
    } else {
        return false;
    }
?>