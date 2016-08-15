<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'ESY Questions';

	$list->SQL = "
        SELECT COALESCE(t0.sieqarefid, 0) as sieqarefid,
               t1.sieqdesc,
               sieqaanswer,
               t1.sieqrefid
          FROM webset.statedef_in_esy_questions AS t1
               LEFT OUTER JOIN webset.std_in_esy_questions_answers AS t0 ON t1.sieqrefid = t0.sieqrefid AND t0.stdrefid = " . $tsRefID . "
         WHERE (recdeactivationdt IS NULL OR NOW()< recdeactivationdt)
         ORDER BY t1.sieqseq, t1.sieqdesc
	";

	$list->addColumn('Question');
	$list->addColumn('Answer')->type('switch');

	$list->editURL = 'javascript:api.goto("' . CoreUtils::getURL('questions_add.php', array('dskey' => $dskey, 'RefID' => 'AF_REFID', 'QuestionID' => 'AF_COL4')) . '");';

	$list->deleteTableName = 'webset.std_in_esy_questions_answers';
	$list->deleteKeyField = 'sieqarefid';

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

?>