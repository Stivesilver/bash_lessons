<?

    $SQL = "
        SELECT count(1)
          FROM webset.std_placementselecteddecision 
         WHERE stdrefid = " . $tsRefID . "
    ";
    
    if (db::execSQL($SQL)->getOne() == "0") {
        return TRUE;
    } else {
        return FALSE;
    }
?>