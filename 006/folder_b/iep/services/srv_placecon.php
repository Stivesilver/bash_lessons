<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student   = new IDEAStudent($tsRefID);

	$list = new ListClass();

	$list->title = 'Placement Considerations - K-12 Students only';

	$list->SQL = "
		SELECT sscmrefid,
			   scmsdesc,
			   scmquestion,
			   scanswer,
			   std.sscmnarrative
		  FROM webset.std_placecon std
			   INNER JOIN webset.statedef_placecon_quest qws ON std.scqrefid = qws.scmrefid
			   INNER JOIN webset.statedef_placecon_answ ans ON ans.scarefid = std.scarefid
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY scmsdesc
	";

	$list->addColumn('Placement');
	$list->addColumn('Question');
	$list->addColumn('Answer');
	$list->addColumn('Narrative');

	$list->addURL = CoreUtils::getURL('srv_placecon_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('srv_placecon_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_placecon';
	$list->deleteKeyField = 'sscmrefid';

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled(db::execSQL("
					   SELECT 1
						 FROM webset.statedef_placecon_quest
						WHERE screfid = " . VNDState::factory()->id . "
						  AND scmlinksw = 'N'
						  AND scmrefid NOT IN (SELECT scmrefid
												 FROM webset.std_placecon
													  INNER JOIN webset.statedef_placecon_quest ON scqrefid = scmrefid
												WHERE stdrefid = " . $tsRefID . ")")->getOne() != '1');

	if ($student->get('ecflag') == 'Y') {
		$list->getButton(ListClassButton::ADD_NEW)
			->disabled(true);
	}

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

	if ($student->get('ecflag') == 'Y') {
		$message = 'Add New Disabled for EC Students';
		print UIMessage::factory($message, UIMessage::NOTE)->toHTML();
	}
?>
