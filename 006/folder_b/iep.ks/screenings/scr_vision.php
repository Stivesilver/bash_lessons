
<?php

    Security::init();

    $smrefid = io::geti('smrefid');

    $SQL = "
        SELECT svrefid
          FROM webset.std_screening_vsn_rslt
         WHERE smrefid = " . io::get("smrefid") . "
    ";
    $svRefID = (int) db::execSQL($SQL)->getOne();

    $edit = new EditClass('edit1', $svRefID);

    $edit->title = 'Vision Test Results';

    $edit->setSourceTable('webset.std_screening_vsn_rslt', 'svrefid');

    $edit->addGroup('Right Eye');
    $edit->addControl('Overall Result', 'select')
        ->sqlField('svre_rslt')
        ->data(
            array(
                'P' => 'Pass',
                'F' => 'Fail',
                'R' => 'Rescreen'
            )
    );

    $edit->addControl('Far Test Result Measurement', 'edit')
        ->sqlField('svre_far_rslt_me')
        ->size(12)
        ->maxlength(12);

    $edit->addControl('Far Test Result Comments', 'edit')
        ->sqlField('svre_far_rslt_co')
        ->size(60)
        ->maxlength(60);

    $edit->addControl('Near Test Result Measurement', 'edit')
        ->sqlField('svre_near_rslt_me')
        ->size(12)
        ->maxlength(12);

    $edit->addControl('Near Test Result Comments', 'edit')
        ->sqlField('svre_near_rslt_co')
        ->size(60)
        ->maxlength(60);

    $edit->addGroup('Left Eye');
    $edit->addControl('Overall Result', 'select')
        ->sqlField('svle_rslt')
        ->data(
            array(
                'P' => 'Pass',
                'F' => 'Fail',
                'R' => 'Rescreen'
            )
    );

    $edit->addControl('Far Test Result Measurement', 'edit')
        ->sqlField('svle_far_rslt_me')
        ->size(12)
        ->maxlength(12);

    $edit->addControl('Far Test Result Comments', 'edit')
        ->sqlField('svle_far_rslt_co')
        ->size(60)
        ->maxlength(60);

    $edit->addControl('Near Test Result Measurement', 'edit')
        ->sqlField('svle_near_rslt_me')
        ->size(12)
        ->maxlength(12);

    $edit->addControl('Near Test Result Comments', 'edit')
        ->sqlField('svle_near_rslt_co')
        ->size(60)
        ->maxlength(60);

    $edit->addGroup('General');
    $edit->addControl('Eye Test Comments', 'textarea')
        ->sqlField('sve_test_co');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Screen ID', 'hidden')->value($smrefid)->sqlField('smrefid');

    $edit->finishURL = CoreUtils::getURL('scr_vision.php', array('smrefid' => $smrefid));
    $edit->cancelURL = CoreUtils::getURL('scr_vision.php', array('smrefid' => $smrefid));

    $edit->saveAndAdd = false;
    $edit->firstCellWidth = '30%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_screening_vsn_rslt')
            ->setKeyField('svrefid')
            ->applyEditClassMode()
    );
    
    $edit->printEdit();
?>
