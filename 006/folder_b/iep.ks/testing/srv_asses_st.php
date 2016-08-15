<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'State-wide Assessments';

        $list->SQL = "
            SELECT std.sswarefid,
                   state.swadesc,
                   sswanarr
              FROM webset.std_assess_state std
                   INNER JOIN webset.statedef_assess_state state ON std.swarefid = state.swarefid
             WHERE stdrefid =  " . $tsRefID . "
             ORDER BY swaseq, swadesc
        ";

        $list->addColumn('State-wide Assessments');
        $list->addColumn('Details');

        $list->deleteTableName = "webset.std_assess_state";
        $list->deleteKeyField = "sswarefid";

        $list->addURL = CoreUtils::getURL('srv_asses_st.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_asses_st.php', array('dskey' => $dskey));

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

        $edit->title = 'State-wide Assessments';

        $edit->setSourceTable('webset.std_assess_state', 'sswarefid');

        $edit->addGroup('General Information');

        $edit->addControl('State-wide assessment', 'select')
            ->sqlField('swarefid')
            ->name('swarefid')
            ->sql("
               SELECT swarefid, swadesc
                 FROM webset.statedef_assess_state
                WHERE screfid = " . VNDState::factory()->id . "
                  AND (recdeactivationdt IS NULL or now()< recdeactivationdt)  
                ORDER BY swaseq, swadesc
            ")
            ->emptyOption(true)
            ->req();

        $edit->addControl('Details', 'textarea')
            ->sqlField('sswanarr');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_asses_st.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_asses_st.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?> 