<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$QuestionID = io::geti('QuestionID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Add/Edit LRE Question';
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_esy_questions_answers', 'sieqarefid');

	if ($RefID == 0) {
		$SQL = "
            SELECT sieqrefid,
                   sieqdesc
              FROM webset.statedef_in_esy_questions state
             WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
               AND NOT EXISTS (SELECT 1
                                 FROM webset.std_in_esy_questions_answers std
                                WHERE std.sieqrefid = state.sieqrefid
                                  AND stdrefid = " . $tsRefID . ")
              ORDER BY sieqseq, sieqdesc
        ";

		$stdid = db::execSQL("
			SELECT sieqarefid
			  FROM webset.std_in_esy_questions_answers
			 WHERE stdrefid = " . $tsRefID . " 
		  	   AND sieqrefid = " . $QuestionID . "
		")->getOne();
		if ($stdid > 0 || $QuestionID == 0) {
			$QuestionID = db::execSQL($SQL)->getOne();
		}
	} else {
		$SQL = "
            SELECT sieqrefid,
                   sieqdesc
              FROM webset.statedef_in_esy_questions state
             WHERE EXISTS (SELECT 1
                             FROM webset.std_in_esy_questions_answers std
                            WHERE std.sieqrefid = state.sieqrefid
                              AND sieqarefid = " . $RefID . ")
        ";
	}

	$question = db::execSQL("
		SELECT *
		  FROM webset.statedef_in_esy_questions state
		 WHERE sieqrefid = " . $QuestionID . "
	")->assoc();

	$edit->addGroup('General Information');

	$edit->addControl('Question', 'select')
		->sqlField('sieqrefid')
		->name('sieqrefid')
		->value(io::get('QuestionID'))
		->sql($SQL)
		->onChange('javascript:api.goto(api.url("questions_add.php", {"dskey" : "' . $dskey . '", "RefID" : "0", "QuestionID" : this.value}))')
		->req();


	$edit->addControl(
		FFSwitchYN::factory('Answer')
			->sqlField('sieqaanswer')
	)->req();


	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->hide(
			db::execSQL("
				SELECT count(1)
				  FROM webset.statedef_in_esy_questions state
                 WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 	   AND sieqrefid NOT IN (SELECT std.sieqrefid
										   FROM webset.std_in_esy_questions_answers std
									      WHERE stdrefid = " . $tsRefID . ")
			")->getOne() < 2);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('questions.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('questions.php', array('dskey' => $dskey));

	$edit->printEdit();
?>