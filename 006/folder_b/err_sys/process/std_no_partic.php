<?

    $SQL = "
        SELECT count(1) 
          FROM webset.std_iepparticipants 
         WHERE stdrefid = " . $tsRefID . "
    ";

    if (db::execSQL($SQL)->getOne() == '0' and VNDState::factory()->code != 'OH') {
        return TRUE;
    } else {
        return FALSE;
    }
?>