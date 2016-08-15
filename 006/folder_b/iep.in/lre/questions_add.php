<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$QuestionID = io::geti('QuestionID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_id = IDEAFormat::get('id');

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Add/Edit LRE Question';
	$edit->saveAndEdit = TRUE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_lre_questions_answers', 'silqarefid');

	if ($RefID == 0) {
		$SQL = "
            SELECT silqrefid,
                   silqdesc,
                   answer,
                   yesno,
                   helptext
              FROM webset.statedef_in_lre_questions state
             WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND COALESCE(set_id, " . $set_id . ") = " . $set_id . "
               AND NOT EXISTS (SELECT 1
                                 FROM webset.std_in_lre_questions_answers std
                                WHERE std.silqrefid = state.silqrefid
                                  AND stdrefid = " . $tsRefID . ")
              ORDER BY silqseq, silqdesc
        ";
		
		$stdid = db::execSQL("
			SELECT silqarefid
			  FROM webset.std_in_lre_questions_answers
			 WHERE stdrefid = ".$tsRefID." 
		  	   AND silqrefid = ".$QuestionID."
		")->getOne();
		if ($stdid > 0 || $QuestionID == 0) {
			$QuestionID = db::execSQL($SQL)->getOne();
		}
	} else {
		$SQL = "
            SELECT silqrefid,
                   silqdesc,
                   answer,
                   yesno,
                   helptext
              FROM webset.statedef_in_lre_questions state
             WHERE EXISTS (SELECT 1
                             FROM webset.std_in_lre_questions_answers std
                            WHERE std.silqrefid = state.silqrefid
                              AND silqarefid = " . $RefID . ")
        ";
	}

	$question = db::execSQL("
		SELECT *
		  FROM webset.statedef_in_lre_questions state
		 WHERE silqrefid = " . $QuestionID . "
	")->assoc();

	$edit->addGroup('General Information');

	$edit->addControl('Question', 'select')
		->sqlField('silqrefid')
		->name('silqrefid')
		->value(io::get('QuestionID'))
		->sql($SQL)
		->onChange('javascript:api.goto(api.url("questions_add.php", {"dskey" : "' . $dskey . '", "RefID" : "0", "QuestionID" : this.value}))')
		->req();

	if ($question['yesno'] == 'Y') {
		$edit->addControl(
			FFSwitchYN::factory('Answer')
				->sqlField('silqaanswersw')
		)->req();
	}

	if ($question['answer'] == 'Y') {
		$edit->addControl('Comments', 'textarea')
			->sqlField('qarejectiondesc')
			->name('qarejectiondesc')
			->css('width', '100%')
			->css('height', '150px')
			->req();
	}
	
	if ($question['helptext'] != '') {
		$edit->addControl('', 'protected')
			->append(UIMessage::factory($question['helptext'], UIMessage::NOTE)->toHTML());		
	}

	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->hide(
			db::execSQL("
				SELECT count(1)
				  FROM webset.statedef_in_lre_questions state
                 WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
				   AND COALESCE(set_id, " . $set_id . ") = " . $set_id . "
			 	   AND silqrefid NOT IN (SELECT std.silqrefid
										   FROM webset.std_in_lre_questions_answers std
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