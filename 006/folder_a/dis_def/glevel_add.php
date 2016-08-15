<?php
    Security::init();
    
    $edit = new EditClass('edit', io::geti('RefID'));
    $edit->title = 'Add/Edit Grade Level';

    $edit->setSourceTable('c_manager.def_grade_levels', 'gl_refid');

    $edit->addGroup('General Information');
    $edit->addControl('Grade Code')->sqlField('gl_code')->name('gl_code')->size(5)->maxlength(2)->req();
    $edit->addControl('Description', 'textarea')->sqlField('gl_desc')->css('width', '100%');
    $edit->addControl('Numeric Value', 'integer')->sqlField('gl_numeric_value')->size(5);
    $edit->addControl('Graduation Years', 'integer')->sqlField('gl_graduation_years')->size(5);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('Such Grade already exists', 
            "
            SELECT 1 
              FROM c_manager.def_grade_levels
             WHERE vndrefid = VNDREFID
               AND gl_code = '[gl_code]'
               AND gl_refid!=AF_REFID
    ");

    $edit->cancelURL = 'glevels.php';
    $edit->finishURL = 'glevels.php';

    $edit->printEdit();
?>
