<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $iepmode = io::get('iepmode');

    $list = new ListClass();

    $list->title = "IEP Meeting Participants";

    $list->SQL = "
    	SELECT spirefid ,
               participantname ,
               participantrole ,
               participantatttype,
               std_seq_num
          FROM webset.std_iepparticipants
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY std_seq_num, participantname
    ";

    $list->addColumn("Participant");
    $list->addColumn("Role");
    $list->addColumn("Attendance Type");
    $list->addColumn("Sequence Number");

    $list->addRecordsProcess('Populate')
        ->url(CoreUtils::getURL('meet_populate_process.ajax.php', array('dskey' => $dskey, 'iepmode' => $iepmode)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->onProcessDone('close_n_reload()');

    $list->printList();
?>
<script type="text/javascript">
    function close_n_reload() {
        api.window.dispatchEvent("users_populated");
        api.window.destroy();
    }
</script>