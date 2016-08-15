<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $cur_eval_id= IDEAStudentEval::factory($tsRefID)->getCurEvalProc();

    $RefIDs = array_map('intval', explode(',', io::post('RefID')));
    $error = null;
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            if ($RefIDs[$i] == $cur_eval_id) {
                $error = 'Current Evaluation Process can not be deleted.';
            } else {
                DBImportRecord::factory('webset.es_std_evalproc', 'eprefid')
                    ->key('eprefid', $RefIDs[$i])
                    ->set('stdrefid', null)
                    ->set('delrefid', 'stdrefid', true)
                    ->setUpdateInformation()
                    ->import(DBImportRecord::UPDATE_ONLY);
            }
        }
    }
    if ($error) io::msg($error, false);
?>
