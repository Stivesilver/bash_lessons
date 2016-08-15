<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $constr = io::get('constr');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $student = new IDEAStudent($tsRefID);
    $screenURL = $ds->safeGet('screenURL');

    $values = '<values>' . chr(10);
    foreach ($_POST as $key => $val) {
        if ($val != '' and substr($key, 0, 7) == 'constr_') $values .= '<value name="' . substr($key, 7, strlen($key)) . '">' . stripslashes($val) . '</value>' . chr(10);
    }
    $values .= '</values>' . chr(10);

	if (io::get('iep') == 'no') {
		$iepyear = "NULL";
	} else {
		$iepyear = $stdIEPYear;
	}
	if (io::get('other_id') == '') {
		$other_id = 'NULL';
	} else {
		$other_id = io::get('other_id');
	}
	$RefID = db::execSQL("
		SELECT refid
		  FROM webset.std_constructions
		 WHERE stdrefid = " .  $tsRefID . "
		   AND iepyear " . ($iepyear == "NULL" ? " IS " : " = ") . $iepyear . "
		   AND other_id " . ($other_id == "NULL" ? " IS " : " = ") . $other_id . "
		   AND constr_id = " . io::geti("constr") . "
	")->getOne();

    if ($RefID > 0) {
        DBImportRecord::factory('webset.std_constructions', 'refid')
            ->key('refid', $RefID)
            ->set('values', base64_encode($values))
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    } else {
        DBImportRecord::factory('webset.std_constructions', 'refid')
            ->set('stdrefid', $tsRefID)
            ->set('iepyear', $iepyear, true)
            ->set('constr_id', io::geti('constr'))
            ->set('other_id', $other_id, true)
            ->set('values', base64_encode($values))
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    }

    if (io::post('finishFlag') == 'no') {
        header('Location: ' . CoreUtils::getURL('main.php', $_GET));
    } elseif (io::get('list') == 'yes') {
        unset($_GET['RefID']);
        header('Location: ' . CoreUtils::getURL('main.php', $_GET));
    } elseif (io::get('nexttab') != '') {
        header('Location: ' . CoreUtils::getURL('main.php', array_merge($_GET, array('tabgo' => 'yes'))));
    } else {
        header('Location: ' . CoreUtils::getURL($screenURL, $_GET));
    }
?>
