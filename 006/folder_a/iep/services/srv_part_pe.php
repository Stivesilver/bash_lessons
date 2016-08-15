<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Participation in PE';

        $list->SQL = "
            SELECT pperefid,
                   ppedtext,
                   epdnarrtext,
                   std.lastuser,
                   std.lastupdate
              FROM webset.std_part_pe std
                   INNER JOIN webset.statedef_part_pe state ON std.ppedrefid = state.ppedrefid 
             WHERE std.stdrefid = " . $tsRefID . "
             ORDER BY ppedtext
        ";

        $list->addColumn('Participation');
        $list->addColumn('Narrative');
        $list->addColumn('Last User');
        $list->addColumn('Last Update')->type('date');

        $list->deleteTableName = 'webset.std_part_pe';
        $list->deleteKeyField = 'pperefid';

        $list->addURL = CoreUtils::getURL('srv_part_pe.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_part_pe.php', array('dskey' => $dskey));

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

        $edit->title = 'Participation in PE';

        $edit->setSourceTable('webset.std_part_pe', 'pperefid');

        $edit->addGroup('General Information');
        $edit->addControl("Participation", "select_radio")
            ->sqlField('ppedrefid')
            ->name('ppedrefid')
            ->sql("
               SELECT ppedrefid, 
                      ppedtext 
                 FROM webset.statedef_part_pe 
                WHERE screfid = " . VNDState::factory()->id . "                   
                ORDER BY ppedtext
            ")
            ->value('')
            ->breakRow()
            ->req();
        $edit->addControl("Narrative", "textarea")->sqlField('epdnarrtext')->css("width", "100%");

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->addSQLConstraint('Such Participation already exists', "
                SELECT 1 
                  FROM webset.std_part_pe
                 WHERE stdrefid = " . $tsRefID . "
                   AND ppedrefid = '[ppedrefid]'
                   AND pperefid!=AF_REFID
        ");

        $edit->finishURL = CoreUtils::getURL('srv_part_pe.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_part_pe.php', array('dskey' => $dskey));

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    }
?>
