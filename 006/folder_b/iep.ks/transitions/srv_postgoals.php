<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');
    $cat_id = 1;

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'Anticipated Post Secondary Goal(s)';

        $list->SQL = "
            SELECT tsnsrefid, 
                   tsndesc,
                   tsnnarr
              FROM webset.std_tsn
                   INNER JOIN webset.statedef_tsn ON tsnstatedefrefid = tsnrefid
             WHERE stdrefid = " . $tsRefID . "
               AND tsncatrefid = " . $cat_id . "
             ORDER BY tsndesc

        ";

        $list->addColumn('Anticipated Post Secondary Goal');
        $list->addColumn('Details');

        $list->deleteTableName = "webset.std_tsn";
        $list->deleteKeyField = "tsnsrefid";

        $list->addURL = CoreUtils::getURL('srv_postgoals.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_postgoals.php', array('dskey' => $dskey));

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

        $edit = new EditClass("edit1", $RefID);

        $edit->title = 'Anticipated Post Secondary Goal';

        $edit->setSourceTable('webset.std_tsn', 'tsnsrefid');

        $edit->addGroup('General Information');

        $edit->addControl('Goal', 'select')
            ->sqlField('tsnstatedefrefid')
            ->sql("
               SELECT tsnrefid, tsndesc
                 FROM webset.statedef_tsn
                WHERE tsncatrefid = " . $cat_id . "
                  AND screfid = " . VNDState::factory()->id . "
                  AND (recdeactivationdt IS NULL OR NOW() < recdeactivationdt)  
                ORDER BY tsndesc
            ")
            ->req();

        $edit->addControl('Details', 'textarea')
            ->sqlField('tsnnarr')
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_postgoals.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_postgoals.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?> 