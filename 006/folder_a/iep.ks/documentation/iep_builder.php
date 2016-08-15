<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdrefid = $ds->safeGet('stdrefid');
    $where = '';

    $list = new ListClass();

    $list->title = 'IEP Builder';

    if (!(SystemCore::$AccessType == "1" and IDEACore::disParam(90) != "N")) {
        $where = "AND (iep_status != 'I' or iep_status is Null)";
    }

    $list->SQL = "
        SELECT siepmrefid,
               rptype,
               siepmtdesc,
               siepmdocdate,
               iep.stdiepmeetingdt,
               iep.lastuser,
               iep.lastupdate,
               iep_status
          FROM webset.std_iep iep
               INNER JOIN webset.sys_teacherstudentassignment ts ON iep.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.statedef_ieptypes types ON iep.siepmtrefid = types.siepmtrefid
         WHERE ts.stdrefid = " . $stdrefid . "
               $where
         ORDER BY iep.lastupdate DESC
    ";

    $list->addColumn('Report Type')->dataCallback('markInactive');
    $list->addColumn('IEP Type')->dataCallback('markInactive');
    $list->addColumn('Archive Date')->type('date')->dataCallback('markInactive');
    $list->addColumn('IEP Meeting Date')->type('date')->dataCallback('markInactive');
    $list->addColumn('Archived By')->dataCallback('markInactive');
    $list->addColumn('Archived On')->type('datetime')->dataCallback('markInactive');

    $list->addURL = CoreUtils::getURL('iep_main.php', array('dskey' => $dskey));
    $list->editURL = "javascript:api.ajax.process(ProcessType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/iep_view.ajax.php') . "', {'RefID' : AF_REFID}))";

    $list->multipleEdit = false;

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_iep')
            ->setKeyField('siepmrefid')
            ->applyListClassMode()
    );

    if (SystemCore::$AccessType == "1") {
        $list->addRecordsProcess('Disable')
            ->message('Do you really want to delete this IEP?')
            ->url(CoreUtils::getURL('iep_delete.ajax.php', array('dskey' => $dskey)))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);
    }

    $list->printList();

    function markInactive ($data, $col) {
        if ($data['iep_status'] == 'I') {
            if ($col == 2) $data[$col] .= ' [DISABLED]';
            return UILayout::factory()
                    ->addHTML($data[$col], '[color:gray; font-style: italic;]')
                    ->toHTML();
        } else {
            return $data[$col];
        }
    }

?>