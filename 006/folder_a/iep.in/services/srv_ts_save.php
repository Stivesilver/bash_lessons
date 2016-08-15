<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

    DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
        ->key('tsrefid', $tsRefID)
        ->set('stdtransitioneligibilitysw', io::post('stdage'))
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    DBImportRecord::factory('webset.std_in_ts', 'stdrefid')
        ->key('stdrefid', $tsRefID)
        ->set('parentguide', io::post('parentguide'))
        ->set('question1sw', io::post('question1sw'))
        ->set('tsscrefid', io::post('tsscrefid'))
        ->set('course_other', io::post('course_other'))
        ->set('tsdrrefid', io::post('tsdrrefid'))
        ->set('question2sw', io::post('question2sw'))
        ->set('question3sw', io::post('question3sw'))
        ->set('question4sw', io::post('question4sw'))
        ->set('trass', io::post('trass'))
        ->set('summary', io::post('summary'))
        ->set('liveindepend', io::post('liveindepend'))
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    if (io::post('finishFlag') == 'no') {
        header('Location: ' . CoreUtils::getURL('srv_ts.php', array('dskey' => $dskey)));
    } else {
        header('Location: ' . CoreUtils::getURL($screenURL, array('dskey' => $dskey)));
    }
?>
