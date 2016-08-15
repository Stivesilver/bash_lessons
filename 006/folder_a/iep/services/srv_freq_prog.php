<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$RefID = io::get('RefID');
	$screenURL = $ds->safeGet('screenURL');
	$nexttab = io::geti('nexttab');

	$updateInf = db::execSQL("
		SELECT lastuser, lastupdate
		  FROM webset.std_freqprogrep
		 WHERE stdrefid = $tsRefID
	")->index();

	$recordID = db::execSQL("
		SELECT tsrefid
		  FROM webset.sys_teacherstudentassignment
		 WHERE tsrefid = $tsRefID
	")->getOne();

	if ($recordID) {
		$edit = new EditClass('edit1', $tsRefID);
	} else {
		$edit = new EditClass('edit1', 0);
	}

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->title = 'Frequency Progress Reporting';

	$edit->addGroup('General Information');
	$edit->addControl(FFMultiSelect::factory())
		->sqlField('fprrefid')
		->rows(9)
		->name('fprrefid')
		->sqlTable(
			'webset.std_freqprogrep',
			'stdrefid',
			array(
				'stdrefid' => $tsRefID,
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			)
		)
		->sql("
               SELECT fprrefid,
                      fprdesc
                 FROM webset.statedef_freqprogrep
                WHERE screfid = " . VNDState::factory()->id . "
                  AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
                  AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                  AND fprdesc != 'Other'
                ORDER BY CASE fprdesc WHEN 'Other' THEN 'z' ELSE fprdesc END
        ");

	$edit->addControl(FFInput::factory())
		->hide(true)
		->sqlField('tsrefid')
		->value($tsRefID);

	$edit->addControl(FFInput::factory())
		->caption('If other, specify')
		->name('other_desc')
		->width(420)
		->value(IDEAStudentRegistry::readStdKey($tsRefID, "mo_iep", "frequency_progress_reporting_other"));

	$edit->setPresaveCallback('preSave', './srv_req_prog.inc.php', array('tsrefid' => $tsRefID));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_freqprogrep')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(!empty($updateInf) ? $updateInf[0] : SystemCore::$userUID);
	$edit->addControl('Last Update', 'protected')->value(!empty($updateInf) ? $updateInf[1] : date('m-d-Y H:i:s'));

	if ($nexttab > 0) {
		$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	} else {
		$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	}

	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));

	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->firstCellWidth = "30%";

	$edit->printEdit();
?>
