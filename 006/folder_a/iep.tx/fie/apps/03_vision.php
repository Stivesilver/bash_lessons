<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_fie_general (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_fie_general
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear
                           )
      	";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT vrefid
		  FROM webset_tx.std_fie_general
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->title       = "Vision/Hearing";
	$edit->saveAndEdit = true;
	$edit->cancelURL   = CoreUtils::getURL('03_vision.php', array('dskey' => $dskey));

	$edit->setSourceTable('webset_tx.std_fie_general', 'vrefid');

	$edit->addGroup("General Information");
	$edit->addControl("Vision", "select_radio")
		->sqlField('visionok')
		->name('visionok')
		->data(
			array(
				'Y' => 'Within normal limits',
				'N' => 'Not within normal limits'
			)
		);

	$edit->addControl("Glasses", "select_radio")
		->sqlField('vision_glass')
		->showIf('visionok', 'Y')
		->data(
			array(
				'O' => 'Without glasses',
				'W' => 'With glasses'
			)
		);

	$edit->addControl("Hearing", "select_radio")
		->sqlField('hearingok')
		->name('hearingok')
		->data(
			array(
				'Y' => 'Within normal limits',
				'N' => 'Not within normal limits'
			)
		);

	$edit->addControl("Aid", "select_radio")
		->sqlField('hearing_aid')
		->data(
			array(
				'U' => 'Unaided',
				'A' => 'Aided'
			)
		)
		->showIf('hearingok', 'Y');

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL  = 'javascript:parent.selectNext()';
	$edit->saveAndAdd = false;

    $edit->printEdit();

?>