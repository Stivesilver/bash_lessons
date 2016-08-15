<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_fluency (stdrefid, iepyear)
            SELECT $tsRefID,
                   $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_fluency
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear
              				   )
        ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_fluency
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_fluency', 'refid');

	$edit->title = "Fluency";

	$edit->addGroup("General Information");
	$edit->addControl("The following formal assessment was administered:", "textarea")
		->sqlField('formal')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Informal Assessment procedures used:", "textarea")
		->sqlField('informal')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->displaySelectAllButton(false)
		->sqlField('stim_cognitive')
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->displaySelectAllButton(false)
		->sqlField('stim_relative')
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('stim_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->displaySelectAllButton(false)
		->sqlField('stim_significant')
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('stim_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Comments", "textarea")
		->sqlField('comments')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');

	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->saveAndAdd     = false;
	$edit->firstCellWidth = "30%";
	$edit->finishURL      = 'javascript:parent.selectNext()';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>