<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit ESY Services Locations';
        
    $edit->setSourceTable('webset.disdef_esy_serv_loc', 'desldrefid');

    $edit->addGroup('General Information');
    $edit->addControl('ESY Services Location')->sqlField('deslddesc')->name('deslddesc')->size(40)->req();
    $edit->addControl(FFSwitchYN::factory('Active Record'))->sqlField('desldactivesw');
        
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('Such Location already exists', 
        "
        SELECT 1 
          FROM webset.disdef_esy_serv_loc
         WHERE vndrefid = VNDREFID
           AND deslddesc = '[deslddesc]'
           AND desldrefid!=AF_REFID
    ");

    $edit->finishURL = 'esy_serv_loc.php';
    $edit->cancelURL = 'esy_serv_loc.php';

    $edit->firstCellWidth = '30%';
     
    $edit->printEdit();

?>
