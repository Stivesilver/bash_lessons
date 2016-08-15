<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
    $screenURL = $ds->safeGet('screenURL');

    $dataSQL = "
        SELECT TO_CHAR(stdiepmeetingdt, 'mm-dd-yyyy')   as stdiepmeetingdt,
               TO_CHAR(stdenrolldt, 'mm-dd-yyyy')       as stdenrolldt,
               TO_CHAR(stdcmpltdt, 'mm-dd-yyyy')        as stdcmpltdt,
               TO_CHAR(stdevaldt, 'mm-dd-yyyy')         as stdevaldt,
               TO_CHAR(stdtriennialdt, 'mm-dd-yyyy')    as stdtriennialdt,
               TO_CHAR(stddraftiepcopydt, 'mm-dd-yyyy') as stddraftiepcopydt,
               TO_CHAR(stdiepcopydt, 'mm-dd-yyyy')      as stdiepcopydt
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = " . $tsRefID . "
    ";

    $oldData = db::execSQL($dataSQL)->assocAll();
    $oldData = $oldData[0];

    DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
        ->key('tsrefid', $tsRefID)
        ->set('stdiepmeetingdt', io::post('stdiepmeetingdt'))
        ->set('stdenrolldt', io::post('stdenrolldt'))
        ->set('stdcmpltdt', io::post('stdcmpltdt'))
        ->set('stdevaldt', io::post('stdevaldt'))
        ->set('stdtriennialdt', io::post('stdtriennialdt'))
        ->set('stddraftiepcopydt', io::post('stddraftiepcopydt'))
        ->set('stdiepcopydt', io::post('stdiepcopydt'))
        ->set('addcomments', io::post('addcomments'))        
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    $newData = db::execSQL($dataSQL)->assocAll();
    $newData = $newData[0];

    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPMTDate.=>', $newData["stdiepmeetingdt"], $oldData["stdiepmeetingdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPInitDate.=>', $newData["stdenrolldt"], $oldData["stdenrolldt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPPrjAnlRvwDate.=>', $newData["stdcmpltdt"], $oldData["stdcmpltdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPEvalDate.=>', $newData["stdevaldt"], $oldData["stdevaldt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPTRIDATE.=>', $newData["stdtriennialdt"], $oldData["stdtriennialdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPDraftCopyDate.=>', $newData["stddraftiepcopydt"], $oldData["stddraftiepcopydt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPCopyProvidedDate.=>', $newData["stdiepcopydt"], $oldData["stdiepcopydt"]);

	DBImportRecord::factory('webset_tx.std_dates', 'drefid')
        ->key('stdrefid', $tsRefID)
        ->key('iepyear', $stdIEPYear)
        ->set('longard', io::post('longard'))
        ->set('briefard', io::post('briefard'))
        ->set('amendment', io::post('amendment'))
        ->set('inituni', io::post('inituni'))
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();
		
    if (io::post('finishFlag') == 'no') {
        header('Location: ' . CoreUtils::getURL('meet_iepdates.php', array('dskey' => $dskey)));
    } else {
        io::js('parent.switchTab(1)');
    }
?>
