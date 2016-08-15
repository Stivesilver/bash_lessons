<?php
    $SQL = "
        SELECT COUNT(1)
          FROM webset.std_disabilitymst
         WHERE sdtype = 1
           AND stdrefid = ".$tsRefID."
    ";

    if (db::execSQL($SQL)->getOne() > 1) {
        return true;
    } else {
        return false;
    }
?>