<?php
    Security::init();
   
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);    
    $tsRefID   = $ds->safeGet('tsRefID');
    $grefid    = io::geti('grefid');
    $brefid    = io::geti('brefid');
    $esy       = io::get('ESY');

    $edit->title = 'Add/Edit Current Progress';
    
    if ($brefid>0){
        $edit = new EditClass('edit1', $brefid);
        $edit->setSourceTable('webset.std_bgb_benchmark', 'brefid');
    } else {
        $edit = new EditClass('edit1', $grefid);
        $edit->setSourceTable('webset.std_bgb_goal', 'grefid');
    }    

    $edit->addGroup('General Information');
    $edit->addControl(
         FFInput::factory('int_range')
            ->caption('Progress')
            ->sqlField('percentofprogress')
            ->limit(0, 100)
    );
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');            
    
    $edit->finishURL = CoreUtils::getURL('bgb_currentProgress.php', array('dskey'=>$dskey, 'ESY'=>$esy));
    $edit->cancelURL = CoreUtils::getURL('bgb_currentProgress.php', array('dskey'=>$dskey, 'ESY'=>$esy));

    $edit->saveAndAdd = '';

    $edit->printEdit();

?>