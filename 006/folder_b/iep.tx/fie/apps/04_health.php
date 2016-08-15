<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "INSERT INTO webset_tx.std_fie_general (stdrefid, iepyear)
            SELECT $tsRefID, $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_fie_general
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear)";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT vrefid
		  FROM webset_tx.std_fie_general
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "40%";
	$edit->title          = "Health History";
	$edit->cancelURL      = CoreUtils::getURL('04_health.php', array('dskey' => $dskey));

	$edit->setSourceTable('webset_tx.std_fie_general', 'vrefid');

	$edit->addGroup("General Information");
	$edit->addControl(
		FFSwitchYN::factory("
			Is there a significant health history
		")
	)
	->sqlField('health_history')
	->name('health_history');

	$edit->addControl("If yes, specify", "textarea")
	->sqlField('health_history_text')
	->css("width", "100%")
	->css("height", "70px")
	->showIf('health_history', 'Y');

	$edit->addControl(
		FFSwitchYN::factory("
			This student appears to have one or
            more physical conditions which directly
            affect his/her ability to benefit
            from the educational process
       ")
	)
	->sqlField('health_condition')
	->name('health_condition');

	$edit->addControl("If yes, specify", "textarea")
		->sqlField('health_condition_text')
		->css("width", "100%")
		->css("height", "70px")
		->showIf('health_condition', 'Y');

	$edit->addControl(
		FFSwitchYN::factory("
			Adapted physical education is indicated
		")
	)
	->sqlField('health_adaptive')
	->name('health_adaptive');

	$edit->addControl("If yes, explain", "textarea")
		->sqlField('health_adaptive_text')
		->css("width", "100%")
		->css("height", "70px")
		->showIf('health_adaptive', 'Y');

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