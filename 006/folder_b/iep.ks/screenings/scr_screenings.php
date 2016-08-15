<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'Add/Edit Screening';

        $list->SQL = "
            SELECT smrefid,
                   smdt,
                   t2.stdesc,
                   t3.sordesc,
                   smusrestablished
              FROM webset.std_screening_mst AS t1
                   LEFT OUTER JOIN webset.def_screening_type AS t2 ON t2.strefid = t1.strefid
                   LEFT OUTER JOIN webset.statedef_screening_ovrl_rslt AS t3 ON t3.sorrefid = t1.sorrefid
                   LEFT OUTER JOIN public.sys_voumst AS t4 ON t4.vourefid = t1.smattsch
             WHERE t1.stdrefid = " . $tsRefID . "
             ORDER BY t1.smdt DESC
        ";

        $list->addColumn("Screening Date")->type('date');
        $list->addColumn("Type of Screening");
        $list->addColumn("Overall Test Result");
        $list->addColumn("Established By");

        $list->deleteTableName = "webset.std_screening_mst";
        $list->deleteKeyField = "smrefid";

        $list->addURL = CoreUtils::getURL('scr_screenings.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('scr_screenings.php', array('dskey' => $dskey));

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

        $edit->title = 'Screenings';

        $edit->setSourceTable('webset.std_screening_mst', 'smrefid');

        $edit->addGroup('General Information');

        $edit->addControl('Screening Date', 'date')->sqlField('smdt');

        $edit->addControl('Type of Screening', 'select_radio')
            ->sqlField('strefid')
            ->sql("              
                SELECT strefid, stdesc
                 FROM webset.def_screening_type
                ORDER BY stdesc
            ")
            ->disabled($RefID > 0)
            ->req();

        $edit->addControl('Overall Test Result', 'select_radio')
            ->sqlField('sorrefid')
            ->sql("              
                SELECT sorrefid, 
                       sordesc
                  FROM webset.statedef_screening_ovrl_rslt
                 WHERE scRefID = " . VNDState::factory()->id . "
                 ORDER BY sordesc
            ")
            ->req();

        $edit->addControl('Established By', 'protected')->value($RefID == 0 ? SystemCore::$userUID : '')->sqlField('smusrestablished');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('scr_screenings.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('scr_screenings.php', array('dskey' => $dskey));

        $edit->saveAndEdit = true;
        $edit->saveAndAdd = false;

        $edit->printEdit();

        if ($RefID > 0) {
            $screen_type = db::execSQL("
                SELECT strefid
                  FROM webset.std_screening_mst
                 WHERE smrefid = " . $RefID . " 
            ")->assoc();
            $tabs = new UITabs('tabs');
            if ($screen_type['strefid'] == '1') {
                $tabs->addTab('Vision')->url(CoreUtils::getURL('scr_vision.php', array('smrefid' => $RefID)));
            } elseif ($screen_type['strefid'] == '2') {
                $tabs->addTab('Hearing')->url(CoreUtils::getURL('scr_hearing.php', array('smrefid' => $RefID)));
            }
            print $tabs->toHTML();
        }
    }
?> 