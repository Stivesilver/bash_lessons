<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$year = db::execSQL("
		SELECT in_test_quest
    	  FROM webset.std_common
         WHERE stdrefid = " . $tsRefID . "		   
	")->assoc();
	
	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'State/District Testing';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '50%';

	$edit->setSourceTable('webset.std_common', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Please select the school year for which the testing accommodations are being made', 'select')
		->sqlField('in_test_quest')
		->name('in_test_quest')
		->sql("
			SELECT dsyrefid, dsydesc
			  FROM webset.disdef_schoolyear
			 WHERE vndrefid = VNDREFID
			 ORDER BY dsybgdt DESC
			");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_common')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->printEdit();

	if ($year['in_test_quest'] > 0) {

		$list = new listClass();

		$list->title = 'Testing Questions';

		$list->SQL = "		
			SELECT COALESCE(sitairefid, 0),
				   t1.ditaitext,
				   t0.sitainarrtext,
				   t1.ditairefid,
				   t0.sitairefid
			  FROM webset.disdef_in_test_assessment_info AS t1
				   LEFT OUTER JOIN webset.std_in_test_assessment_info AS t0 ON t1.ditairefid = t0.ditairefid
			   AND t0.stdrefid = " . $tsRefID . "
			   AND t0.dsyrefid = " . $year['in_test_quest'] . "
			 WHERE vndrefid = VNDREFID
			 ORDER BY t1.ditairefid
		";

		$list->addColumn('Question');
		$list->addColumn('Answer')->dataCallback('checkAnswer');
	
		$list->editURL = 'javascript:api.goto("' . CoreUtils::getURL('sdt_top_ass_add.php', array('dskey' => $dskey, 'RefID' => 'AF_REFID', 'QuestionID' => 'AF_COL3')) . '");';

		$list->deleteTableName = 'webset.std_in_test_assessment_info';
		$list->deleteKeyField = 'sitairefid';

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($list->deleteTableName)
				->setKeyField($list->deleteKeyField)
				->applyListClassMode()
		);

		$list->addButton(
			IDEAFormat::getPrintButton(array('dskey' => $dskey))
		);

		$list->printList();
	}
	
	function checkAnswer($data, $col) {
		if ($data['sitairefid'] > 0) {
			return $data['sitainarrtext'];
		} else {
			return UIMessage::factory('NOT ADDRESSED', UIMessage::NOTE)
					->toHTML();
		}
	}
?>