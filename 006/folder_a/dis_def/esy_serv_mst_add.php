<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit ESY service';
    
    $edit->setSourceTable('webset.disdef_esy_services', 'desdrefid');

    $edit->addGroup('General Information');
    $edit->addControl('ESY service', 'edit')->sqlField('desddesc')->name('desddesc')->size(40)->req();
    $edit->addControl(FFSwitchYN::factory('Active Record'))->sqlField('desdactivesw');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('Such Service already exists', 
        "
        SELECT 1 
          FROM webset.disdef_esy_services
         WHERE vndrefid = VNDREFID
           AND desddesc = '[desddesc]'
           AND desdrefid!=AF_REFID
    ");

    $edit->finishURL = 'esy_serv_mst.php';
    $edit->cancelURL = 'esy_serv_mst.php';
    
    $edit->firstCellWidth = '30%';

    $edit->printEdit();

?>
