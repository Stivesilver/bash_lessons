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

    DBImportRecord::factory('webset.std_in_eligibility')
        ->key('stdrefid', $tsRefID)
        ->set('edccdeval', chkField('edccdeval'), TRUE)
        ->set('edncdeval', chkField('edncdeval'), TRUE)
        ->set('lastuser', db::escape(SystemCore::$userUID))
        ->set('lastupdate', 'NOW()', true)
        ->import();
    
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
    
    $oldData = db::execSQL($dataSQL)->assocAll();
    $oldData = $oldData[0];
     
    $SQL = "
        UPDATE webset.sys_teacherstudentassignment
           SET stdiepmeetingdt   =  ".chkField('stdiepmeetingdt').",
               stdenrolldt       =  ".chkField('stdenrolldt').",
               stdcmpltdt        =  ".chkField('stdcmpltdt').",
               stdevaldt         =  ".chkField('stdevaldt').",
               stdtriennialdt    =  ".chkField('stdtriennialdt').",
               stddraftiepcopydt =  ".chkField('stddraftiepcopydt').",
               stdiepcopydt      =  ".chkField('stdiepcopydt').",
               parentrightdt     =  ".chkField('parentrightdt').",
               addComments       =  ".chkField('addcomments').",
               previousiepdt     =  ".chkField('previousiepdt').",
               lastuser          =  ".chkField('lastuser').",
               lastupdate        = NOW()
         WHERE tsrefid = ".$tsRefID."
    ";    
    db::execSQL($SQL);
    
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
