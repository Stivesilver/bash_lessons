<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$area_id = 120;

	$refid = (int) db::execSQL("
		SELECT refid
    	  FROM webset.std_general
         WHERE stdrefid = " . $tsRefID . "
		   AND area_id = " . $area_id . "
	")->getOne();

	$edit = new EditClass("edit1", $refid);

	$edit->title = 'Individual Learning Plan';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('Eligibility', 'select_check')
		->sqlField('txt01')
		->sql("
			SELECT refid,
                   validvalue
              FROM webset.disdef_validvalues
             WHERE vndrefid = VNDREFID
               AND valuename = 'IN_ILP_Levels'
               AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = 1
             ORDER BY sequence_number, validvalue ASC
		")
		->breakRow();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>