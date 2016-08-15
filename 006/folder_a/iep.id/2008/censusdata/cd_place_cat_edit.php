<?php

    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $stateID    = VNDState::factory()->id;
    $edit       = new EditClass("edit1", io::get("RefID"));

    $edit->setSourceTable('webset.std_placementcode', 'pcrefid');

    $edit->title = "Add/Edit State LRE Reporting";

    $edit->addGroup("General Information");

    $edit->addControl(
        FFMultiSelect::factory('Dec 1 Category')
            ->sql("
				SELECT plc.spcrefid,
                       plc.spccode || ' - ' || plc.spcdesc
                  FROM webset.statedef_placementcategorycode plc
                       INNER JOIN webset.statedef_placementcategorycodetype ec ON plc.spctrefid = ec.spctrefid
                 WHERE plc.screfid = $stateID
                   AND (plc.recdeactivationdt IS NULL or now()< plc.recdeactivationdt)
                 ORDER BY 2, plc.spcCode
             ")
            ->name('spcrefid')
            ->maxRecords(1)
    )
    ->sqlField('spcrefid');

    $edit->addGroup("Duration Information");

    $edit->addControl("Start Date", "date")
         ->sqlField('spcbeg');

    $edit->addControl("End Date", "date")
         ->sqlField('spcend');

    $edit->addGroup("Update Information", true);

    $edit->addControl("Last User", "protected")
         ->value(SystemCore::$userUID)
         ->sqlField('lastuser');

    $edit->addControl("Last Update", "protected")
         ->value(date("m-d-Y H:i:s"))
         ->sqlField('lastupdate');

    $edit->addControl("stdrefid", "hidden")
         ->value($tsRefID)
         ->sqlField('stdrefid');

    $edit->printEdit();

?>