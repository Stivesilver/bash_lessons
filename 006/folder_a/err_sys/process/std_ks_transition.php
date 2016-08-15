<?

    $SQL = "
        SELECT count(1)
          FROM webset.std_tsn
               INNER JOIN webset.statedef_tsn ON webset.std_tsn.tsnstatedefrefid = webset.statedef_tsn.tsnrefid
         WHERE webset.std_tsn.stdrefid = " . $tsRefID . "
           AND tsnnarrsw = 'Y'
           AND tsncatrefid = 1
           AND (tsnnarr IS Null OR trim(tsnnarr) = '')
    ";

    if (db::execSQL($SQL)->getOne() > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
?>
