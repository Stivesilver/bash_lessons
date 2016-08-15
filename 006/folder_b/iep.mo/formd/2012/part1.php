<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

    $SQL = "
        SELECT refid,
               studentage
          FROM webset.std_form_d
         WHERE stdrefid = " . $tsRefID . "
           AND syrefid  = " . $stdIEPYear . "
    ";
    $data = db::execSQL($SQL)->assoc();

    $edit = new EditClass('edit1', $stdIEPYear);

    $edit->title = 'Form D - Part 1: State Assessments';

    $edit->setSourceTable('webset.std_form_d', 'syrefid');

    $edit->addGroup("General Information");
    $edit->addControl("Student Age", "select_radio")
        ->name('studentage')
        ->sqlField('studentage')
        ->data(
            IDEACore::disParam(98) == 'Y' ?
                array(
                '1' => 'Students in Grade 3-8',
                '2' => 'Students in Grades 9-12',
                '3' => 'Grades 3-8 and 9-12'
                ) :
                array(
                '1' => 'Students in Grade 3-8',
                '2' => 'Students in Grades 9-12'
                )
        )
        ->breakRow()
        ->onChange('var edit1 = EditClass.get(); edit1.saveAndEdit();');

    if ($data['studentage'] == 3) {
        $edit->addGroup("Grades 3-8: Assessment");
    }

    $edit->addControl(
        FFCheckBox::factory("The student will participate in the Grade-Level Assessment")
            ->baseValue('Y')
            ->sqlField('young_map')
            ->showIf('studentage', '3')
    );

    $edit->addControl(
        FFCheckBox::factory("Without accommodations")
            ->baseValue('Y')
            ->sqlField('young_o')
            ->showIf('studentage', '3')
    );

    $edit->addControl(
        FFCheckBox::factory("With accommodations")
            ->baseValue('Y')
            ->sqlField('young_w')
            ->showIf('studentage', '3')
    );

    if ($data['studentage'] == 1) {
        $edit->addGroup("Grades 3-8: Assessment");
    } else {
        $edit->addGroup("Grades 9-12: Assessment");
    }

    $edit->addControl(
        FFCheckBox::factory($data['studentage'] == 1 ? "The student will participate in the Grade-Level Assessment" : "The student will participate in required End of Course (EOC) Assessments")
            ->baseValue('Y')
            ->sqlField('assessments')
    );

    $edit->addControl(
        FFCheckBox::factory("Without accommodations")
            ->baseValue('Y')
            ->sqlField('accommparticip')
    );

    $edit->addControl(
        FFCheckBox::factory("With accommodations")
            ->baseValue('Y')
            ->sqlField('accommmath')
    );


    if ($data['studentage'] != 1) {
        $edit->addGroup("Grades 9-12: Additional EOC Assessment");
    }

    $edit->addControl(
        FFCheckBox::factory("The IEP team has determined the student will participate in the following optional EOC Assessment(s):")
            ->baseValue('Y')
            ->sqlField('addeoc_m')
            ->hideIf('studentage', '1')
    );


    $edit->addControl("Specify Assessments", "textarea")
        ->sqlField('addeoc_ass')
        ->css("width", "100%")
        ->css("height", "30px")
        ->hideIf('studentage', '1');

    $edit->addControl(
        FFCheckBox::factory("Without accommodations")
            ->baseValue('Y')
            ->sqlField('addeoc_o')
            ->hideIf('studentage', '1')
    );

    $edit->addControl("If Without Specify Please", "select_check")
        ->sqlField('addeoc_os')
        ->data(
            array(
                '15' => 'Geometry',
                '11' => 'Algebra II'
            )
        )
        ->hideIf('studentage', '1');

    $edit->addControl(
        FFCheckBox::factory("With accommodations")
            ->baseValue('Y')
            ->sqlField('addeoc_w')
            ->hideIf('studentage', '1')
    );

    $edit->addControl("If With Specify Please", "select_check")
        ->sqlField('addeoc_ws')
        ->data(
            array(
                '15' => 'Geometry',
                '11' => 'Algebra II'
            )
        )
        ->hideIf('studentage', '1');

    if ($data['studentage'] != 1) {
        $edit->addGroup("Grades 9-12: Exempt");
    }
    $edit->addControl(
        FFCheckBox::factory("The IEP team has determined the student is exempt from the following Additional EOC Assessment(s):")
            ->baseValue('Y')
            ->sqlField('exempt')
            ->hideIf('studentage', '1')
    );

    $edit->addControl("Specify Assessments", "textarea")
        ->sqlField('exempt_ass')
        ->css("width", "100%")
        ->css("height", "30px")
        ->hideIf('studentage', '1');


    $edit->addControl("If Exempt Specify Please", "select_check")
        ->sqlField('exempts')
        ->data(
            array(
                '15' => 'Geometry',
                '11' => 'Algebra II'
            )
        )
        ->hideIf('studentage', '1');

    $edit->addGroup("MAP-A");
    if ($data['studentage'] != 2) {
        $edit->addControl(
            FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate; therefore, is excluded from Grade-Level Assessment participation")
                ->baseValue('Y')
                ->name('eligible')
                ->sqlField('eligible')
        );
    } else {
        $edit->addControl(
            FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate  for grades 10-11  (MAP-A); therefore is excluded from EOC participation")
                ->baseValue('Y')
                ->name('eligible')
                ->sqlField('eligible')
        );
    }

    $edit->addControl("Include a statement of why the child cannot participate in the regular assessment (Grade-Level or EOC)", "textarea")
        ->sqlField('statement')
        ->css("width", "100%")
        ->css("height", "50px")
        ->showIf('eligible', 'Y');

    $edit->addControl("Explain why the alternate assessment selected is appropriate.", "textarea")
        ->sqlField('alternate')
        ->css("width", "100%")
        ->css("height", "50px")
        ->showIf('eligible', 'Y');

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
    $edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

    $edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
    $edit->cancelURL = 'javascript:api.window.destroy();';

    $edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '50%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_form_d')
            ->setKeyField('syrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();

    include("notes1.php");
?>
