<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	
	$caption = (int) db::execSQL("
		SELECT disitext
    	  FROM webset.disdef_in_srv_init
	     WHERE vndrefid = VNDREFID
	")->getOne();
	
	if ($caption == '') {
		$caption = 'The student is graduating or reaching age eligibility limit.';
	}
	
	$edit = new EditClass("edit1", $tsRefID);
	
	$edit->title = 'Initiation and Duration of Services Question';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '50%';
	
	$edit->setSourceTable('webset.std_in_srv_init', 'stdrefid');

	$edit->addGroup('General Information');
	$edit->addControl(FFSwitchYN::factory($caption)->emptyOption(TRUE)->breakRow())
		->sqlField('sisisw');

	$edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_srv_init')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>