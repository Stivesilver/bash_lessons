<?php
    Security::init();

    $dskey   = io::get('dskey');
    $ds        = DataStorage::factory($dskey);    
    $tsRefID   = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');    
    
    function chkField($name){
        if (io::post($name)=="0001-01-01 00:00:00" || io::post($name)=="") {
            $val = "NULL";
        } else {
            $val = "'" . db::escape(io::post($name)) . "'";
        }
        return $val;
    }
    
    $dataSQL = "
        SELECT TO_CHAR(stdiepmeetingdt, 'mm-dd-yyyy')   as stdiepmeetingdt,
               TO_CHAR(stdenrolldt, 'mm-dd-yyyy')       as stdenrolldt,
               TO_CHAR(stdcmpltdt, 'mm-dd-yyyy')        as stdcmpltdt,
               TO_CHAR(stdevaldt, 'mm-dd-yyyy')         as stdevaldt,
               TO_CHAR(stdtriennialdt, 'mm-dd-yyyy')    as stdtriennialdt,
               TO_CHAR(stddraftiepcopydt, 'mm-dd-yyyy') as stddraftiepcopydt,
               TO_CHAR(stdiepcopydt, 'mm-dd-yyyy')      as stdiepcopydt,
               TO_CHAR(previousiepdt, 'mm-dd-yyyy')     as previousiepdt
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = ".$tsRefID."
    ";         
    
    $oldData = db::execSQL($dataSQL)->assoc();

	DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
		->key('tsrefid', $tsRefID)
		->set('stdiepmeetingdt', chkField('stdiepmeetingdt'), true)
		->set('stdcmpltdt', chkField('stdcmpltdt'), true)
		->set('stdevaldt', chkField('stdevaldt'), true)
		->set('stdtriennialdt', chkField('stdtriennialdt'), true)
		->set('stddraftiepcopydt', chkField('stddraftiepcopydt'), true)
		->set('stdiepcopydt', chkField('stdiepcopydt'), true)
		->set('parentrightdt', chkField('parentrightdt'), true)
		->set('amendment', chkField('amendment'), true)
		->set('addcomments', chkField('addcomments'), true)
		->set('previousiepdt', chkField('previousiepdt'), true)
		->set('stdenrolldt', chkField('stdenrolldt'), true)
		->set('ks_cur_iep', chkField('ks_cur_iep'), true)
		->set('ks_trs_iep', chkField('ks_trs_iep'), true)
		->set('ks_cmp_iep', chkField('ks_cmp_iep'), true)
		->set('lastuser', SystemCore::$userUID)
		->set('lastupdate', 'NOW()', true)
		->import();

	DBImportRecord::factory('webset.std_common', 'sfrefid')
		->key('stdrefid', $tsRefID)
		->set('uni_field3', chkField('uni_field3'), true)
		->set('uni_field5', chkField('uni_field5'), true)
		->set('receiveddate', chkField('receiveddate'), true)
		->set('lastuser', SystemCore::$userUID)
		->set('lastupdate', 'NOW()', true)
		->import();

	$newData = db::execSQL($dataSQL)->assocAll();
    $newData = $newData[0];
    
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPMTDate.=>',           $newData["stdiepmeetingdt"],   $oldData["stdiepmeetingdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPInitDate.=>',         $newData["stdenrolldt"],       $oldData["stdenrolldt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPPrjAnlRvwDate.=>',    $newData["stdcmpltdt"],        $oldData["stdcmpltdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPEvalDate.=>',         $newData["stdevaldt"],         $oldData["stdevaldt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPTRIDATE.=>',          $newData["stdtriennialdt"],    $oldData["stdtriennialdt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPDraftCopyDate.=>',    $newData["stddraftiepcopydt"], $oldData["stddraftiepcopydt"]);
    IDEAStudentEvent::addEvent($tsRefID, '<=.IEPCopyProvidedDate.=>', $newData["stdiepcopydt"],      $oldData["stdiepcopydt"]);
    
    if (io::post('finishFlag')=='no') {
        header('Location: '.CoreUtils::getURL('meet_iepdates.php', array('dskey'=>$dskey)));
    } else {
        header('Location: '.CoreUtils::getURL($screenURL, array('dskey'=>$dskey)));
    }
?>
