<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $student = new IDEAStudent($tsRefID);
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'Supports For School Personnel';

        $list->SQL = "
                SELECT sspmrefid,
                       sspdesc || COALESCE(' (' || sspnarrative|| ') ', '') as sspdesc,
                       sspbegdate,
                       sspenddate,
                       srv_minutes,
                       srv_weeks,
                       sfdesc,
                       crtdesc,
                       nasw
                  FROM webset.std_srv_supppersonnel std
                       INNER JOIN webset.statedef_services_supppersonnel state ON std.ssprefid = state.ssprefid
                       LEFT JOIN webset.disdef_frequency ON sfrefid = freq_id
                       LEFT JOIN webset.disdef_location ON crtrefid = loc_id
                 WHERE std.stdrefid = " . $tsRefID . "
                 ORDER BY sspdesc, sspbegdate
        ";

        $list->addColumn("Description");
        $list->addColumn("Beginning Date")->type('date')->dataCallback('clearNAservice');
        $list->addColumn("Ending Date")->type('date')->dataCallback('clearNAservice');
        $list->addColumn("Minutes")->dataCallback('clearNAservice');
        $list->addColumn("Weeks")->dataCallback('clearNAservice');
        $list->addColumn("Frequency")->dataCallback('clearNAservice');
        $list->addColumn("Location")->dataCallback('clearNAservice');

        $list->deleteTableName = "webset.std_srv_supppersonnel";
        $list->deleteKeyField = "sspmrefid";

        $list->addURL = CoreUtils::getURL('srv_supp_pers.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_supp_pers.php', array('dskey' => $dskey));

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->addButton(
            IDEAFormat::getPrintButton(array('dskey' => $dskey))
        );

        $list->printList();
    } else {

        $naswSQL = "
            SELECT ssprefid
              FROM webset.statedef_services_supppersonnel
             WHERE screfid = " . VNDState::factory()->id . "
               AND nasw = 'Y'
        ";
        $id_na = db::execSQL($naswSQL)->indexAll();

        $edit = new EditClass("edit1", $RefID);

        $edit->title = 'Supports For School Personnel';

        $edit->setSourceTable('webset.std_srv_supppersonnel', 'sspmrefid');

        $edit->addGroup('General Information');

        $edit->addControl('Service', 'select')
            ->sqlField('ssprefid')
            ->name('ssprefid')
            ->sql("
               SELECT ssprefid, sspdesc
                FROM webset.statedef_services_supppersonnel
               WHERE screfid = " . VNDState::factory()->id . "
                 AND (enddate IS NULL or now()< enddate)
               ORDER BY seqnum, CASE substring(lower(sspdesc), 1, 5)  WHEN 'other' THEN 'zzz' ELSE sspdesc END
            ")
            ->emptyOption(true)
            ->req();

        $edit->addControl('Services comment', 'textarea')
            ->sqlField('sspnarrative')
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('Begin Date', 'date')
            ->sqlField('sspbegdate')
            ->value($student->getDate('stdiepyearbgdt'))
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('End Date', 'date')
            ->sqlField('sspenddate')
            ->value($student->getDate('stdiepyearendt'))
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('Minutes')
            ->sqlField('srv_minutes')
            ->size(5)
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('Weeks')
            ->sqlField('srv_weeks')
            ->size(30)
            ->value(IDEACore::disParam(134))
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('Frequency', 'select')
            ->sqlField('freq_id')
            ->sql("
                SELECT sfrefid, sfdesc
                  FROM webset.disdef_frequency
                 WHERE (enddate>now() or enddate is Null)
                   AND vndrefid = VNDREFID
                 ORDER BY sfdesc
            ")
            ->hideIf('ssprefid', $id_na);

        $edit->addControl('Location', 'select')
            ->sqlField('loc_id')
            ->sql("
                SELECT crtrefid, crtdesc
                 FROM webset.disdef_location
                WHERE (enddate>now() or enddate is Null)
                  AND vndrefid = " . $_SESSION["s_VndRefID"] . "
                ORDER BY crtdesc
            ")
            ->hideIf('ssprefid', $id_na);

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_supp_pers.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_supp_pers.php', array('dskey' => $dskey));

        $edit->printEdit();
    }

    function clearNAservice($data, $col) {
        if ($data['nasw'] == 'Y') {
            return '';
        } else {
            return $data[$col];
        }
    }

?>
