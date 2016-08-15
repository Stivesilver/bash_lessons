<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Special Transportation';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '60%';

	$edit->setSourceTable('webset.std_in_special_transportation', 'stdrefid');

	$edit->addGroup('General Information');
	
	$edit->addControl('If <b>special</b> transportation is needed, will this transportation result in excess transit time?', 'select')
		->sqlField('sistsptransneededexcesstimequestion')
		->data(
			array(
				'N/A'=>'N/A',
				'Yes'=>'Yes',
				'No'=>'No'
			)
		)
		->emptyOption(TRUE);
	
	$edit->addControl('If yes, is this excess transit time needed to meet the needs of the student as determined by the case conference committee?', 'select')
		->sqlField('sistsptransneededconfcommquestion')
		->data(
			array(			
				'Yes'=>'Yes',
				'No'=>'No'
			)
		)
		->emptyOption(TRUE);	

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_special_transportation')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>