<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$set_ini = IDEAFormat::getIniOptions();
	$iepYearTitle = array_key_exists('iep_year_title', $set_ini) ? $set_ini['iep_year_title'] : 'IEP Year';
	$iepTitle = array_key_exists('iep_title', $set_ini) ? $set_ini['iep_title'] : 'IEP';

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Select ' . $iepYearTitle;
	$edit->setSourceTable('webset.std_iep_year', 'siymrefid');

	$edit->saveAndAdd = false;
    $edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Save Year');

    $edit->addGroup('General Information');
    $edit->addControl('Anticipated ' . $iepTitle . ' Initiation Date', 'protected')
        ->sqlField('siymiepbegdate');

    $edit->addControl('Anticipated ' . $iepTitle . ' Annual Review Date', 'protected')
        ->sqlField('siymiependdate');

	$edit->addUpdateInformation();

	$edit->addControl('Student ID', 'hidden')
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->setPresaveCallback('setCurrentYear', './iep_cur_save.inc.php', array('dskey' => $dskey));

    $edit->saveLocal = false;
    $edit->firstCellWidth = '40%';

    $edit->printEdit();
?>
