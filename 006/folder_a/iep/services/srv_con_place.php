<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$RefID = io::get('RefID');
	$screenURL = $ds->safeGet('screenURL');

	$updateInf = db::execSQL("
		SELECT lastuser, lastupdate
		  FROM webset.std_placementconsiderations
		 WHERE stdrefid = $tsRefID
	")->index();

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Placements Considered';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup('General Information');

	$edit->addControl("Placement Considered", "select_check")
		->sqlField('pcdrefid')
		->displaySelectAllButton(false)
		->name('pcdrefid')
		->sqlTable(
			'webset.std_placementconsiderations',
			'stdrefid',
			array(
				'stdrefid' => $tsRefID,
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			)
		)
		->sql("
			SELECT pcdrefid,
			       CASE WHEN pcdtype='E' THEN 'EC' ELSE 'K12' END || ' - ' || pcddesc
			  FROM webset.statedef_placementconsiddecision
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 ORDER BY 2
        ")
		->breakRow();

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_placementconsiderations')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(!empty($updateInf) ? $updateInf[0] : SystemCore::$userUID);
	$edit->addControl('Last Update', 'protected')->value(!empty($updateInf) ? $updateInf[1] : date('m-d-Y H:i:s'));
	$edit->addControl(FFInput::factory())
		->hide(true)
		->sqlField('tsrefid')
		->value($tsRefID);


	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->firstCellWidth = "30%";

	$edit->printEdit();
?>
