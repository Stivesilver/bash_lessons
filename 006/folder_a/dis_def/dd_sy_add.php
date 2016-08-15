<?php
    Security::init();

    $edit = new EditClass('edit1', io::geti('RefID'));
    $edit->title = 'Add/Edit District School Year';

    $edit->setSourceTable('webset.disdef_schoolyear', 'dsyrefid');
    
    $edit->addGroup('General Information');
    $edit->addControl('School Year Description', 'edit')->sqlField('dsydesc')->name('dsydesc')->size(40)->req();
    $edit->addControl('Start of school year', 'date')->sqlField('dsybgdt')->name('dsybgdt')->req();        
    $edit->addControl('End of school year', 'date')->sqlField('dsyendt')->name('dsyendt')->req();
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

    $edit->finishURL = 'dd_sy.php';
    $edit->cancelURL = 'dd_sy.php';
    
    $edit->addSQLConstraint('School Year with such title, start or end date already exists', 
        "
        SELECT 1 
          FROM webset.disdef_schoolyear
         WHERE vndrefid = VNDREFID
           AND (dsydesc = '[dsydesc]' OR dsybgdt = '[dsybgdt]' OR dsyendt = '[dsyendt]')
           AND dsyrefid!=AF_REFID
    ");

    $edit->addSQLConstraint('End Date should be greater than Start Date.', 
        "SELECT 1 WHERE ('[dsybgdt]'::date >'[dsyendt]'::date)");
                
    $edit->firstCellWidth = '30%';

    $edit->printEdit();

?>
