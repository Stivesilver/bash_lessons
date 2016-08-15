<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Placement Selected Decisions';

        $list->SQL = "
            SELECT spdrefid,
                   description,
                   std.lastuser,
                   std.lastupdate
              FROM webset.std_placementselecteddecision std
                   INNER JOIN webset.statedef_placementselectdecisions state ON std.sspsdrefid = state.sspsdrefid 
             WHERE std.stdrefid = " . $tsRefID . "
             ORDER BY description
        ";

        $list->addColumn('Placement Considered');
        $list->addColumn('Last User');
        $list->addColumn('Last Update')->type('date');

        $list->deleteTableName = 'webset.std_placementselecteddecision';
        $list->deleteKeyField = 'spdrefid';

        $list->addURL = CoreUtils::getURL('srv_sel_place_dec.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_sel_place_dec.php', array('dskey' => $dskey));

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

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Placement Selected Decisions';

        $edit->setSourceTable('webset.std_placementselecteddecision', 'spdrefid');

        $edit->addGroup('General Information');
        $edit->addControl("Decision", "select_radio")
            ->sqlField('sspsdrefid')
            ->name('sspsdrefid')
            ->sql("
               SELECT sspsdrefid, 
                      description 
                 FROM webset.statedef_placementselectdecisions 
                WHERE screfid = " . VNDState::factory()->id . "                   
                  AND (enddate IS NULL or now()< enddate)
                ORDER BY 2
            ")
            ->value('')
            ->breakRow()
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->addSQLConstraint('Such Decision already exists', "
                SELECT 1 
                  FROM webset.std_placementselecteddecision
                 WHERE stdrefid = " . $tsRefID . "
                   AND sspsdrefid = '[sspsdrefid]'
                   AND spdrefid!=AF_REFID
        ");

        $edit->finishURL = CoreUtils::getURL('srv_sel_place_dec.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_sel_place_dec.php', array('dskey' => $dskey));

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    }
?>
