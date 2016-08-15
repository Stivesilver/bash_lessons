<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit Progress Reports Extent';
    
    $edit->setSourceTable('webset.disdef_progressrepext', 'eprefid');

    $edit->addGroup('General Information');
    
    $edit->addControl('Short value')
        ->sqlField('epsdesc')
        ->name('epsdesc')
        ->size(6)
        ->maxlength(6)
        ->req();
    
    $edit->addControl('Complete Description', 'textarea')
        ->sqlField('epldesc')
        ->css('WIDTH', '100%')
        ->css('HEIGHT', '100px')
        ->req();
    
    $edit->addControl('User Help Message', 'textarea')
        ->sqlField('ephelpmsg')
        ->css('WIDTH', '100%')
        ->css('HEIGHT', '100px');
    
    $edit->addControl('Display Sequence', 'integer')->sqlField('epseq');
    $edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('Such code already exists', 
            "
            SELECT 1 
              FROM webset.disdef_progressrepext
             WHERE vndrefid = VNDREFID
               AND (epsdesc = '[epsdesc]')
               AND eprefid!=AF_REFID
    ");

    $edit->finishURL = 'ds_progress_ext.php';
    $edit->cancelURL = 'ds_progress_ext.php';
    
    $edit->firstCellWidth = '30%';

    $edit->printEdit();
    
?>
