<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit District School Year';
    
    $edit->setSourceTable('webset.disdef_esy_dates', 'refid');

    $edit->addGroup('General Information');
    $edit->addControl('Begin Date', 'date')->sqlField('begdate')->name('begdate')->req();
    $edit->addControl('End Date', 'date')->sqlField('enddate')->name('enddate')->req();
        
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
    
    $edit->addSQLConstraint('End Date should be greater than Begining Date', 
            "
            SELECT 1 WHERE '[begdate]' >= '[enddate]' AND '[begdate]'!= '0' AND '[enddate]'!= '0'
    ");

    $edit->finishURL = 'esy_dates.php';
    $edit->cancelURL = 'esy_dates.php';
    
    $edit->firstCellWidth = '30%';
    $edit->saveAndAdd = false;

    $edit->printEdit();

?>
