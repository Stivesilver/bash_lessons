<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$QuestionID = io::geti('QuestionID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$year = db::execSQL("
		SELECT sfrefid,
		       in_test_quest
    	  FROM webset.std_common
         WHERE stdrefid = " . $tsRefID . "		   
	")->assoc();

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Assessment Question';
	$edit->saveAndEdit = TRUE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_test_assessment_info', 'sitairefid');

	if ($RefID == 0) {
		$SQL = "
            SELECT ditairefid,
                   ditaitext,
				   ditainarrsw,
                   proposedoptions,
				   narrttile1,
				   ditainarrsw2,
				   proposedoptions2,
				   narrttile2
              FROM webset.disdef_in_test_assessment_info dis
             WHERE vndrefid = VNDREFID 
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
               AND NOT EXISTS (SELECT 1
                                 FROM webset.std_in_test_assessment_info std
                                WHERE std.ditairefid = dis.ditairefid
                                  AND stdrefid = " . $tsRefID . "
								  AND dsyrefid = " . $year['in_test_quest'] . ")
              ORDER BY ditairefid
        ";

		$stdid = db::execSQL("
			SELECT sitairefid
			  FROM webset.std_in_test_assessment_info
			 WHERE stdrefid = " . $tsRefID . " 
			   AND dsyrefid = " . $year['in_test_quest'] . "
		  	   AND ditairefid = " . $QuestionID . "
		")->getOne();
		if ($stdid > 0 || $QuestionID == 0) {
			$QuestionID = db::execSQL($SQL)->getOne();
		}
	} else {
		$SQL = "
            SELECT ditairefid,
                   ditaitext,
				   ditainarrsw,
                   proposedoptions,
				   narrttile1,
				   ditainarrsw2,
				   proposedoptions2,
				   narrttile2
              FROM webset.disdef_in_test_assessment_info dis
             WHERE EXISTS (SELECT 1
                             FROM webset.std_in_test_assessment_info std
                            WHERE std.ditairefid = dis.ditairefid
                              AND sitairefid = " . $RefID . ")
        ";
	}

	$question = db::execSQL("
		SELECT *
		  FROM webset.disdef_in_test_assessment_info
		 WHERE ditairefid = " . $QuestionID . "
	")->assoc();

	$edit->addGroup('General Information');

	$edit->addControl('Question', 'select')
		->sqlField('ditairefid')
		->name('ditairefid')
		->value(io::get('QuestionID'))
		->sql($SQL)
		->onChange('javascript:api.goto(api.url("sdt_top_ass_add.php", {"dskey" : "' . $dskey . '", "RefID" : "0", "QuestionID" : this.value}))')
		->req();

	if ($question['ditainarrsw'] == 'Y') {
		$title = ($question['narrttile1'] ? $question['narrttile1'] : 'Narrative');
		if ($question['proposedoptions'] != '') {
			$dataArr = array();
			$arr = explode(';', $question['proposedoptions']);
			for ($i = 0; $i < count($arr); $i++) {
				$dataArr[trim($arr[$i])] = trim($arr[$i]);
			}
			$edit->addControl($title, 'select_check')
				->sqlField('sitainarrtext')
				->name('sitainarrtext')
				->data($dataArr)
				->breakRow();
		} else {
			$edit->addControl($title, 'textarea')
				->sqlField('sitainarrtext')
				->name('sitainarrtext')
				->css('width', '100%')
				->css('height', '100px');
		}
	}

	if ($question['ditainarrsw2'] == 'Y') {
		$title = ($question['narrttile2'] ? $question['narrttile2'] : 'Narrative');
		if ($question['proposedoptions2'] != '') {
			$dataArr = array();
			$arr = explode(';', $question['proposedoptions2']);
			for ($i = 0; $i < count($arr); $i++) {
				$dataArr[trim($arr[$i])] = trim($arr[$i]);
			}
			$edit->addControl($title, 'select_check')
				->sqlField('narr2options')
				->name('narr2options')
				->data($dataArr)
				->breakRow();
			$edit->addControl('Other ' . $title, 'textarea')
				->sqlField('narr2other')
				->name('narr2other')
				->css('width', '100%')
				->css('height', '100px');
		} else {
			$edit->addControl($title, 'textarea')
				->sqlField('narr2options')
				->name('narr2options')
				->css('width', '100%')
				->css('height', '100px');
		}
	}

	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->hide(
			db::execSQL("
				SELECT count(1)
				  FROM webset.disdef_in_test_assessment_info 
                 WHERE vndrefid = VNDREFID 
			       AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 	   AND ditairefid NOT IN (SELECT std.ditairefid
										    FROM webset.std_in_test_assessment_info std
									       WHERE stdrefid = " . $tsRefID . "
											 AND dsyrefid = " . $year['in_test_quest'] . ")
			")->getOne() < 2);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Year ID', 'hidden')->value($year['in_test_quest'])->sqlField('dsyrefid');

	$edit->finishURL = CoreUtils::getURL('sdt_top_ass.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('sdt_top_ass.php', array('dskey' => $dskey));

	$edit->printEdit();
?>