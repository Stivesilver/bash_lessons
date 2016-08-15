<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$QuestionID = io::geti('QuestionID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Special Factors to Consider';
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_spconsid', 'sscmrefid');

	if ($RefID == 0) {
		$SQL = "
            SELECT scmrefid,
                   scmquestion
              FROM webset.statedef_spconsid_quest state
             WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
               AND NOT EXISTS (SELECT 1
                                 FROM webset.std_spconsid std
                                WHERE std.scqrefid = state.scmrefid
                                  AND stdrefid = " . $tsRefID . ")
              ORDER BY seqnum, scmquestion
        ";

		$stdid = db::execSQL("
			SELECT sscmrefid
			  FROM webset.std_spconsid
			 WHERE stdrefid = " . $tsRefID . " 
		  	   AND scqrefid = " . $QuestionID . "
		")->getOne();
		if ($stdid > 0 || $QuestionID == 0) {
			$QuestionID = db::execSQL($SQL)->getOne();
		}
	} else {
		$SQL = "
            SELECT scmrefid,
                   scmquestion
              FROM webset.statedef_spconsid_quest state
             WHERE EXISTS (SELECT 1
                             FROM webset.std_spconsid std
                            WHERE std.scqrefid = state.scmrefid
                              AND sscmrefid = " . $RefID . ")
        ";
	}

	$question = db::execSQL("
		SELECT *
		  FROM webset.statedef_spconsid_quest state
		 WHERE scmrefid = " . $QuestionID . "
	")->assoc();

	$edit->addGroup('General Information');

	$edit->addControl('Question', 'select')
		->sqlField('scqrefid')
		->name('scqrefid')
		->value(io::get('QuestionID'))
		->sql($SQL)
		->onChange('javascript:api.goto(api.url("questions_add.php", {"dskey" : "' . $dskey . '", "RefID" : "0", "QuestionID" : this.value}))')
		->req();


	$edit->addControl('Answer', 'select_radio')
		->sqlField('scarefid')
		->sql("
			SELECT scarefid,
				   scanswer
			  FROM webset.statedef_spconsid_answ
			 WHERE scmrefid = VALUE_01
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 ORDER BY scanswer
		")
		->tie('scqrefid')
		->req();


	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->hide(
			db::execSQL("
				SELECT count(1)
				  FROM webset.statedef_spconsid_quest state
                 WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 	   AND scmrefid NOT IN (SELECT std.scqrefid
										  FROM webset.std_spconsid std
									     WHERE stdrefid = " . $tsRefID . ")
			")->getOne() < 2);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));

	$edit->printEdit();
?>