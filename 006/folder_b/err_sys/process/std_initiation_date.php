<?php
    $SQL = "
        SELECT stdenrolldt
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = ".$tsRefID."
    ";

    if (db::execSQL($SQL)->getOne() == '' && VNDState::factory()->code != 'ID') {
        return true;
    } else {
        return false;
    }
?>