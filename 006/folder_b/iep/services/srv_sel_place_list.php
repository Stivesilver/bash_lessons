<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$RefID = io::get('RefID');
	$screenURL = $ds->safeGet('screenURL');

	$updateInf = db::execSQL("
		SELECT lastuser, lastupdate
		  FROM webset.std_placementselected
		 WHERE stdrefid = $tsRefID
	")->index();

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Placements Selected';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup('General Information');

	$edit->addControl("Placement Selected", "select_check")
		->sqlField('sspsdrefid')
		->displaySelectAllButton(false)
		->name('sspsdrefid')
		->sqlTable(
			'webset.std_placementselected',
			'stdrefid',
			array(
				'stdrefid' => $tsRefID,
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			)
		)
		->sql("
			SELECT std.pcdrefid,
                      CASE WHEN pcdtype='E' THEN 'EC' ELSE 'K12' END || ' - ' || pcddesc
                 FROM webset.statedef_placementconsiddecision state
                      INNER JOIN webset.std_placementconsiderations std ON std.pcdrefid = state.pcdrefid
                WHERE std.stdrefid = " . $tsRefID . "
                ORDER BY 2
        ")
		->breakRow();

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_placementselected')
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
