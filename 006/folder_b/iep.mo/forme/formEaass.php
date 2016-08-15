<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $edit = new EditClass('edit1', io::get('RefID'));

    $edit->title = 'Add/Edit  Alternate Assessments';

    $edit->setSourceTable('webset.std_form_e_dtl', 'edrefid');

    $edit->addGroup('General Information');
    $edit->addControl("District Assessment", "textarea")
        ->sqlField('assessment')
        ->css("width", "100%")
        ->css("height", "50px");

    $edit->addControl("Alternate Assessment", "textarea")
        ->sqlField('accomodation')
        ->css("width", "100%")
        ->css("height", "50px");

    $edit->addControl("Why the child cannot participate in the regular assessment", "textarea")
        ->sqlField('assesswhynot')
        ->css("width", "100%")
        ->css("height", "50px");

    $edit->addControl("Why the particular alternate assessment selected is appropriate.", "textarea")
        ->sqlField('assesswhyalt')
        ->css("width", "100%")
        ->css("height", "50px");

    $edit->addGroup('Update Information', true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
    $edit->addControl('Sp Considerations ID', 'hidden')->value(io::geti('spconsid'))->name('spconsid');
    $edit->addControl("Mode", "hidden")->value("A")->sqlField('assmode');

    $edit->finishURL = CoreUtils::getURL('formEmain.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
    $edit->cancelURL = CoreUtils::getURL('formEmain.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

    $edit->firstCellWidth = '40%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_form_e_dtl')
            ->setKeyField('edrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
