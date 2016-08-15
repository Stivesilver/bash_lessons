<?

    $SQL = "
        SELECT 1
          FROM webset.sys_teacherstudentassignment ts
               INNER JOIN sys_usermst usr ON ts.umrefid = usr.umrefid
         WHERE tsrefid = " . $tsRefID . "
           AND usr.vndrefid = VNDREFID
    ";

    if (db::execSQL($SQL)->getOne() != 1) {
        return true;
    } else {
        return false;
    }
?>