<?php
    $SQL = "
        SELECT stdtriennialdt
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = ".$tsRefID."
    ";

    if (db::execSQL($SQL)->getOne() == '') {
        return true;
    } else {
        return false;
    }

?>