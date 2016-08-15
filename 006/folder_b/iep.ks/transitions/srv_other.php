<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $SQL = "SELECT tsnorefid 
              FROM webset.std_tsn_other 
             WHERE stdrefid = " . $tsRefID;

    $result = db::execSQL($SQL);
    if (!$result->EOF) {
        $tsnorefid = $result->fields[0];
    } else {
        $tsnorefid = 0;
    }

    $edit = new EditClass("edit1", $tsnorefid);

    $edit->title = 'Other instructional and/or educational experiences to meet Post Secondary goals/career needs';

    $edit->setSourceTable('webset.std_tsn_other', 'tsnorefid');

    $edit->addGroup('General Information');

    $edit->addControl('Post Secondary Other Instructional Needs', 'textarea')
        ->sqlField('tsnodesc')
        ->css('width', '100%')
        ->css('height', '200px');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_tsn_other')
            ->setKeyField('tsnorefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?> 