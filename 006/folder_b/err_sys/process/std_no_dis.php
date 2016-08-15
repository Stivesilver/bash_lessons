<?php
    $SQL = "
        SELECT sdrefid
          FROM webset.std_disabilitymst
         WHERE stdrefid = ".$tsRefID."
    ";

    if (db::execSQL($SQL)->getOne() == '') {
        return true;
    } else {
        return false;
    }
?>