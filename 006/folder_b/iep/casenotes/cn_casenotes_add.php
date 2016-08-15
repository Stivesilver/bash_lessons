<?php
    Security::init();
    
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');

    $edit = new EditClass('edit1', io::geti('RefID'));
    
    $edit->title = 'Add/Edit Case Notes';
    
    $edit->setSourceTable('webset.std_casenotes', 'cnrefid');
    
    $edit->addGroup('General Information');
    $edit->addControl('Date Event Occurred', 'datetime')->sqlField('eventdt')->value(date('Y-m-d h:i a'));
    $edit->addControl('Case Note Short Desc', 'edit')->sqlField('cnsdesc')->css('width', '100%');
    $edit->addControl('Detailed Case Note Text', 'textarea')
        ->sqlField('cntext')
        ->css('width', '100%')
        ->css('height', '200px');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl('User UID', 'hidden')->value(SystemCore::$userUID)->sqlField('entryuser');

    $edit->finishURL = CoreUtils::getURL('cn_casenotes.php', array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL('cn_casenotes.php', array('dskey'=>$dskey));

    $edit->printEdit();

?>
