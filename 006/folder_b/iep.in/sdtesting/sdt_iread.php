<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();

	$area_id = IDEAAppArea::IN_IREAD;

	$RefID = (int)db::execSQL("
		SELECT refid
		  FROM webset.std_general
		 WHERE stdrefid = $tsRefID
		   AND area_id = $area_id
	")->getOne();

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'IRead';
	$edit->firstCellWidth = '50%';
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addGroup('General Information');
	$edit->addControl('Date', 'date')
		->sqlField('dat01');

	$edit->addControl('Score')
		->sqlField('txt01')
		->size(25)
		->req();

	$edit->addControl('Rating', 'select')
		->sqlField('int01')
		->name('int01')
		->sql("
			SELECT ditrrefid,
				   ditrdesc
			  FROM webset.disdef_in_test_rating
			 ORDER BY ditrrefid
		")
		->emptyOption(true)
		->req();

	$quests = db::execSQL("
   		SELECT refid,
       		   validvalue
  	  	  FROM webset.disdef_validvalues
         WHERE vndrefid = VNDREFID
   		   AND valuename = 'IN_IRead'
   		   AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = '1'
 		 ORDER BY valuename, sequence_number, validvalue ASC
	")->assocAll();

	$edit->addControl('Conference Date', 'date')
		->sqlField('dat02');

	$edit->addGroup('Questions Information');
	foreach ($quests as $question) {
		$edit->addControl(
			FFSwitchYN::factory($question['validvalue'])
				->name('question_' . $question['refid'])
				->value(db::execSQL("
					SELECT (xpath('/record/question_" . $question['refid'] . "/text()', txt02::xml))[1]
					  FROM webset.std_general
					 WHERE refid = " . $RefID . "
				")->getOne()
				)
				->emptyOption(true, 'N/A')
		);
	}

	$edit->setPostsaveCallback('saveAnswers', 'sdt_iread.inc.php');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
