<?php
   	if ($stdIEPYear > 0) {
        $SQL = "
            SELECT pLeadStat
              FROM webset.std_plepmst
             WHERE iepyear  = ".$stdIEPYear."
               AND stdrefid = ".$tsRefID."
        ";

        if (db::execSQL($SQL)->getOne() == '') {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
?>