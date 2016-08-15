<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_recommend (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_recommend
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear
                           )
        ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_recommend
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_recommend', 'refid');

	$edit->title = "Recommendations";

	$edit->addGroup("General Information");
	$edit->addControl("It is recommended that the student receive speech therapy services to address his/her speech impairment. ", "select_check")
		->displaySelectAllButton(false)
		->sqlField('therapy')
		->sql("SELECT 'Y', ''");

	$edit->addControl("It is recommended that the student continue to receive speech therapy services to address his/her speech impairment.", "select_check")
		->displaySelectAllButton(false)
		->sqlField('continue')
		->sql("SELECT 'Y', ''");

	$edit->addControl("The student did not meet criteria as speech impaired, therefore, it is recommended that he/she remain in his/her current educational placement.  ", "select_check")
		->displaySelectAllButton(false)
		->sqlField('remain')
		->sql("SELECT 'Y', ''");

	$edit->addControl("The student no longer meets eligibility criteria as speech impaired, therefore, it is recommended that the speech student be dismissed from speech services.", "select_check")
		->displaySelectAllButton(false)
		->sqlField('dis_miss')
		->sql("SELECT 'Y', ''");

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
	$edit->saveAndAdd     = false;
	$edit->firstCellWidth = "60%";
	$edit->finishURL      = 'javascript:parent.parent.selectNext()';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();