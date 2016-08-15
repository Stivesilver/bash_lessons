<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

    $dataSQL = "
        SELECT TO_CHAR(stdiepmeetingdt, 'mm-dd-yyyy')   as stdiepmeetingdt,
               TO_CHAR(stdcmpltdt, 'mm-dd-yyyy')        as stdcmpltdt,
               TO_CHAR(stdevaldt, 'mm-dd-yyyy')         as stdevaldt,
               TO_CHAR(stdtriennialdt, 'mm-dd-yyyy')    as stdtriennialdt,
               TO_CHAR(stdiepcopydt, 'mm-dd-yyyy')      as stdiepcopydt
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = " . $tsRefID . "
    ";

    $oldData = db::execSQL($dataSQL)->assocAll();
    $oldData = $oldData[0];

    DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
        ->key('tsrefid', $tsRefID)
        ->set('stdiepmeetingdt', io::post('stdiepmeetingdt'))
        ->set('stdcmpltdt', io::post('stdcmpltdt'))
        ->set('stdevaldt', io::post('stdevaldt'))
        ->set('stdtriennialdt', io::post('stdtriennialdt'))
        ->set('stdiepcopydt', io::post('stdiepcopydt'))
        ->set('addcomments', io::post('addcomments'))
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    DBImportRecord::factory('webset.std_common', 'stdrefid')
        ->key('stdrefid', $tsRefID)
        ->set('id_medical', io::post('id_medical'))
        ->set('uni_field1', io::post('uni_field1'))
        ->set('uni_field2', io::post('uni_field2'))
        ->set('uni_field3', io::post('uni_field3'))
        ->set('uni_field4', io::post('uni_field4'))
        ->set('wa_graddate', io::post('wa_graddate'))
        ->set('wa_transother', io::post('wa_transother'))
        ->set('latest_cmda', io::post('latest_cmda'))
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    $newData = db::execSQL($dataSQL)->assocAll();
    $newData = $newData[0];

    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPMTDate.=>', $newData["stdiepmeetingdt"], $oldData["stdiepmeetingdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPPrjAnlRvwDate.=>', $newData["stdcmpltdt"], $oldData["stdcmpltdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPEvalDate.=>', $newData["stdevaldt"], $oldData["stdevaldt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPTRIDATE.=>', $newData["stdtriennialdt"], $oldData["stdtriennialdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPCopyProvidedDate.=>', $newData["stdiepcopydt"], $oldData["stdiepcopydt"]);

    if (io::post('finishFlag') == 'no') {
        header('Location: ' . CoreUtils::getURL('meet_iepdates.php', array('dskey' => $dskey, 'desktop' => io::get('desktop'))));
    } else {
        header('Location: ' . CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop'))));
    }
?>
