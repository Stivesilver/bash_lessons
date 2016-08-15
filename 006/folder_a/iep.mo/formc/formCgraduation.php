<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $SQL = "
        SELECT refid
          FROM webset.std_form_c
         WHERE stdrefid = " . $tsRefID . "
           AND syrefid  = " . $stdIEPYear . "
    ";

    $RefID = (int) db::execSQL($SQL)->getOne();

    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'Graduation';

    $edit->setSourceTable('webset.std_form_c', 'refid');

    $edit->addGroup('General Information');
    $edit->addControl('Student will graduate by', 'select_check')
        ->sqlField('graduate')
        ->sql("
            SELECT validvalueid,
                   validvalue                   
              FROM webset.glb_validvalues
             WHERE ValueName = 'MO_FormC_Grad'
             ORDER BY sequence_number
        ")
        ->breakRow();

    $edit->addControl('Anticipated month and year of graduation')
        ->sqlField('timegrad')
        ->size(10);

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
    $edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

    $edit->finishURL = 'javascript:api.window.destroy();';
    $edit->cancelURL = 'javascript:api.window.destroy();';

    $edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '40%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_form_c')
            ->setKeyField('refid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>