<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = io::geti('area_id');

	#Finds table ID
	$record = db::execSQL("
    	SELECT ssnrefid
          FROM webset.std_srv_notes
         WHERE area_id = " . $area_id . "
           AND tsrefid = " . $tsRefID . "
    ")->getOne();

	#Starts EditClass Page
	$edit = new EditClass('edit1', (int)$record);

	$edit->title = 'Notes';

	$edit->setSourceTable('webset.std_srv_notes', 'ssnrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Notes', 'textarea')
		->autoHeight(true)
		->sqlField('notes');

	$edit->addUpdateInformation();
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('tsrefid');
	$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_srv_notes')
			->setKeyField('ssnrefid')
			->applyEditClassMode()
	);

	$edit->printEdit();

?>
