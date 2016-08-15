<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $stdrefid = $ds->safeGet('stdrefid');
    $where = '';

    $list = new ListClass();

    $list->title = 'IEP Builder';

    if (!(SystemCore::$AccessType == "1" and IDEACore::disParam(90) != "N")) {
        $where = "AND (iep_status!='I' or iep_status is Null)";
    }

    $list->SQL = "
        SELECT siepmrefid,
               siepmdocdate,
               siepmtdesc,
               COALESCE(doctype, '') || COALESCE(siepmerrlogfilenm, ''),
               iep.stdenrolldt,
               iep.lastuser,
               to_char(iep.lastupdate, 'mm-dd-yyyy'),
               iep_status
          FROM webset.std_iep AS iep
               INNER JOIN webset.sys_teacherstudentassignment AS ts ON iep.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.sped_doctype AS doc ON iep.rptype = CAST(drefid as VARCHAR)
               LEFT OUTER JOIN webset.statedef_ieptypes AS types ON iep.siepmtrefid = types.siepmtrefid
         WHERE ts.stdrefid = " . $stdrefid . "
               $where
         ORDER BY iep.siepmdocdate DESC, siepmrefid DESC
    ";

    $list->addColumn('Archive Date')->type('date')->dataCallback('markInactive');
    $list->addColumn('Type of Document')->dataCallback('markInactive');
    $list->addColumn('Report Type')->dataCallback('markInactive');
    $list->addColumn('IEP Initiation Date')->dataCallback('markInactive')->type('date');
    $list->addColumn('Archived By')->dataCallback('markInactive');
    $list->addColumn('Archived On')->dataCallback('markInactive');

    $list->addURL = CoreUtils::getURL('xml_main.php', array('dskey' => $dskey));
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
            ->url(CoreUtils::getURL('xml_delete.ajax.php', array('dskey' => $dskey)))
	        ->width('80px')
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);
    }

    if (IDEACore::disParam(104) == "Y") {
        //$list->addButton('Upload')->onClick('location = "'.CoreUtils::getURL('iep_upload.php', array('dskey'=>$dskey)).'";');
    }

    $list->printList();

    function markInactive($data, $col) {
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
