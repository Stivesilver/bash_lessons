<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_id = IDEAFormat::get('id');
	$set_ini = IDEAFormat::getIniOptions();
	
	$reasonButon = FFMenuButton::factory('Find Reason');

	$reasons = db::execSQL("
		SELECT gecrdesc
		  FROM webset.statedef_in_gecomreasons
		 WHERE COALESCE(set_id, " . $set_id . ") = " . $set_id . "
		 ORDER BY (recdeactivationdt IS NULL or now()< recdeactivationdt)
	")->assocAll();

	for ($i = 0; $i < count($reasons); $i++) {
		$reasonButon->addItem($reasons[$i]['gecrdesc'], '$("#rsn").val($.trim($("#rsn").val() + "\n\n" + '.  json_encode($reasons[$i]['gecrdesc']).'))');
	}

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Removal from General Education Setting';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->topButtons = TRUE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_specificinfodtl', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl($set_ini['in_general_education_removal'], 'textarea')
		->sqlField('gedremreasondesc')
		->name('rsn')
		->css('width', '100%')
		->css('height', '120px')		
		->append(
			$reasonButon
		);
		
	$edit->addGroup('Explain why this student\'s level of performance affects his/her involvement in the general curriculum or preschool activities. Complete only affected areas');

	$edit->addControl('Academically', 'textarea')
		->sqlField('gedinfluence_a')
		->css('width', '100%')
		->css('height', '80px');

	$edit->addControl('Behaviorally/Socially', 'textarea')
		->sqlField('gedinfluence_bs')
		->css('width', '100%')
		->css('height', '80px');

	$edit->addControl('Physically', 'textarea')
		->sqlField('gedinfluence_ph')
		->css('width', '100%')
		->css('height', '80px');

	$edit->addControl('Speech/Language', 'textarea')
		->sqlField('gedinfluence_sl')
		->css('width', '100%')
		->css('height', '80px');
	
	$edit->addObject(UIMessage::factory('Please be sure to use the scroll bars to answer all questions and click on the \'Save and Finish\' button when complete.'));

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_specificinfodtl')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>