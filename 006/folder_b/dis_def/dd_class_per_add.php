<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit Building Class Period';
    
    $edit->setSourceTable('webset.sch_classperiods', 'bcprefid');

    $edit->addGroup('General Information');
    $edit->addControl('Building', 'select')
        ->sqlField('vourefid')
        ->name('vourefid')
        ->sql("
            SELECT vourefid, vouname
              FROM sys_voumst
             WHERE vndrefid = VNDREFID
             ORDER BY  vouname
        ")
        ->req();
    
    $edit->addControl('Building Class Period')->sqlField('bcpdesc')->name('bcpdesc')->size(30)->req();
    $edit->addControl('Sequence', 'integer')->sqlField('bcpseqnumber');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');  
    
    $edit->addSQLConstraint('Such period already exists', 
        "
        SELECT 1 
          FROM webset.sch_classperiods dis
               INNER JOIN public.sys_voumst vou ON dis.vourefid = vou.vourefid
         WHERE bcpdesc = '[bcpdesc]' 
           AND dis.vourefid = [vourefid]
           AND bcprefid!=AF_REFID
    ");

    $edit->finishURL = 'dd_class_per.php';
    $edit->cancelURL = 'dd_class_per.php';
    
    $edit->firstCellWidth = '30%';

    $edit->printEdit();

?>
