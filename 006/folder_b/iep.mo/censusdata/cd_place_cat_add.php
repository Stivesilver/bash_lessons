<?php
    Security::init();

    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
    $title     = $ds->safeGet('Placement Title');
    $ds->clear('Placement Title');

    $edit = new EditClass("edit1", io::geti('RefID'));

    $edit->title = 'Add/Edit '.$title;

    $edit->setSourceTable('webset.std_placementcode', 'pcrefid');

    $edit->addGroup("General Information");
    $edit->addControl("Placement", "select_radio")
        ->sqlField('spcrefid')
        ->name('spcrefid')
        ->sql("
            SELECT plc.spcrefid,
                   CASE spctcode WHEN 'EC' THEN 'EC' ELSE 'K-12' END || ' ' || plc.spccode || ' - ' || plc.spcdesc
              FROM webset.statedef_placementcategorycode plc
                   INNER JOIN webset.statedef_placementcategorycodetype ec ON plc.spctrefid = ec.spctrefid
             WHERE plc.screfid = ".VNDState::factory()->id ."
               AND (plc.recdeactivationdt IS NULL or now()< plc.recdeactivationdt)
             ORDER BY 2, plc.spccode
        ")
	    ->req()
        ->breakRow();

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');

    $edit->addSQLConstraint(
        'You are trying to add duplicate Placement',
        "
        SELECT 1
          FROM webset.std_placementcode
         WHERE stdrefid = " . $tsRefID . "
           AND pcrefid!=AF_REFID
    ");

    $edit->finishURL = CoreUtils::getURL('cd_place_cat.php', array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL('cd_place_cat.php', array('dskey'=>$dskey));

    $edit->saveAndAdd = false;

    $edit->printEdit();

?>
