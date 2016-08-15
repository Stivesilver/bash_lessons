<?php
    Security::init();
    
    $edit = new editClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit State-wide Assessment';
        
    $edit->setSourceTable('webset.statedef_assess_state', 'swarefid');
        
    $edit->addGroup('General Information');
    $edit->addControl('Order #', 'integer')->sqlField('swaseq');
    $edit->addControl('Assessment')->sqlField('swadesc')->css("width", "80%")->req();
    $edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('State ID', 'hidden')->value(VNDState::factory()->id)->sqlField('screfid');

    $edit->finishURL = "assessment_list.php";
    $edit->cancelURL = "assessment_list.php";

    $edit->printEdit();
    
?>