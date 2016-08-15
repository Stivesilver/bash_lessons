<?php
    Security::init();

    $smrefid = io::geti('smrefid');
    
    $SQL = "
        SELECT shrefid
          FROM webset.std_screening_hrng_rslt
         WHERE smrefid = ".io::get("smrefid")."
    ";
    $shRefID = (int)db::execSQL($SQL)->getOne();

    $edit = new EditClass('edit1', $shRefID);
        
    $edit->setSourceTable('webset.std_screening_hrng_rslt', 'shrefid');

    $edit->title = 'Hearing Test Results';

    $edit->addGroup('General Information');
    $edit->addControl('Overall Result', 'select')
        ->sqlField('sh_rslt')
        ->data(
            array(
                'P' => 'Pass',
                'F' => 'Fail'
            )
        );
        
    $edit->addControl('Filtered Words Standard Score', 'edit')
        ->sqlField('sh_fw_stnd_sc')
        ->size(40)
        ->maxlength(40);
        
    $edit->addControl('Filtered Words Standard Percentile', 'edit')
        ->sqlField('sh_fw_stnd_pe')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Auditory Figure Ground Standard Score', 'edit')
        ->sqlField('sh_afg_stnd_sc')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Auditory Figure Ground Standard Percentile', 'edit')
        ->sqlField('sh_afg_stnd_pe')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Competing Words Standard Score', 'edit')
        ->sqlField('sh_cw_stnd_sc')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Competing Words Standard Percentile', 'edit')
        ->sqlField('sh_cw_stnd_pe')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Scan Composite Standard Score', 'edit')
        ->sqlField('sh_sc_stnd_sc')
        ->size(40)
        ->maxlength(40);
        
    $edit->addControl('Scan Composite Standard Percentile', 'edit')
        ->sqlField('sh_sc_stnd_pe')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Test Interpretation Standard Score', 'edit')
        ->sqlField('sh_ti_stnd_sc')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Test Interpretation Standard Percentile', 'edit')
        ->sqlField('sh_ti_stnd_pe')
        ->size(40)
        ->maxlength(40);

    $edit->addControl('Test Comments', 'textarea')
        ->sqlField('sh_test_co');

    $edit->addGroup('Update Information', true);       
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('Screen ID', 'hidden')->value($smrefid)->sqlField('smrefid');
        
    $edit->finishURL = CoreUtils::getURL('scr_hearing.php', array('smrefid'=>$smrefid));
    $edit->cancelURL = CoreUtils::getURL('scr_hearing.php', array('smrefid'=>$smrefid));
        
    $edit->saveAndAdd = false;
    $edit->firstCellWidth  = '35%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_screening_hrng_rslt')
            ->setKeyField('shrefid')
            ->applyEditClassMode()
    );
    
    $edit->printEdit();
?>
