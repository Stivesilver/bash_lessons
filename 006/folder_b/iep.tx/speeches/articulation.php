<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_articulation (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1
         					 FROM webset_tx.std_speech_articulation
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear
                           )
        ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_articulation
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_articulation', 'refid');

	$edit->title = "Articulation";

	$edit->addGroup("General Information");
	$edit->addTab("Part 1");
	$edit->addControl("The following formal assessment was administered:", "textarea")
		->sqlField('formal')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("The student achieved a percentile of:", "edit")
		->sqlField('percentile')
		->size(50)
		->maxlength(3);

	$edit->addControl("Informal Assessment procedures used:", "textarea")
		->sqlField('informal')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Phonemes and position in error:", "textarea")
		->sqlField('phonemes')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Phonological processes present:", "textarea")
		->sqlField('phonological')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addTab("Part 2");
	$edit->addControl("Stimulable phonemes: ", "textarea")
		->sqlField('stimulable')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->sqlField('stim_cognitive')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->sqlField('stim_relative')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('stim_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->sqlField('stim_significant')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('stim_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Comments");
	$edit->addControl("Comments", "textarea")
		->sqlField('comments')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "30%";

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL  = 'javascript:parent.parent.selectNext()';
	$edit->saveAndAdd = false;

	$edit->printEdit();

?>