<?php
    Security::init();
    
    $edit = new editClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit Course';
        
    $edit->setSourceTable('webset.disdef_tsn', 'tsnrefid');
        
    $edit->addGroup('General Information');
    $edit->addControl('Course #', 'integer')->sqlField('tsnnum')->req();
    $edit->addControl('Course Description')->sqlField('tsndesc')->css("width", "80%")->req();    
    $edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

    $edit->finishURL = "courses.php";
    $edit->cancelURL = "courses.php";

    $edit->printEdit();
    
?>