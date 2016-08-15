<?php
    Security::init();

    $edit = new EditClass('edit', io::geti('RefID'));

    $edit->title = 'Add/Edit Role';
    
    $edit->setSourceTable('webset.disdef_participantrolesdef', 'prdrefid');

    $edit->addGroup('General Information');
    $edit->addControl('Role')->sqlField('prddesc')->name('prddesc')->size(80)->req();
    $edit->addControl('Sequence number', 'integer')->sqlField('seq_num');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('Such Role already exists', 
        "
        SELECT 1 
          FROM webset.disdef_participantrolesdef
         WHERE vndrefid = VNDREFID
           AND prddesc = '[prddesc]'
           AND prdrefid!=AF_REFID
    ");

    $edit->finishURL = 'role.php';
    $edit->cancelURL = 'role.php';

    $edit->printEdit();

?>
