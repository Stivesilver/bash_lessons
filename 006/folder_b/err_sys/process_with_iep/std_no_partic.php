<?php
    if ($stdIEPYear > 0) {
        $SQL = "
            SELECT COUNT(1)
              FROM webset.std_iepparticipants
             WHERE iep_year = ".$stdIEPYear."
               AND stdrefid = ".$tsRefID."
        ";

        if (db::execSQL($SQL)->getOne() == '0') {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
?>