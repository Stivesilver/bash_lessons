<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$set_id = IDEAFormat::get('id');

	$list = new listClass();

	$list->title = 'LRE Questions';

	$list->SQL = "
        SELECT COALESCE(t0.silqarefid, 0) as silqarefid,
               t1.silqdesc,
               CASE silqaanswersw WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END as answer,
               qarejectiondesc,
               t1.silqrefid
          FROM webset.statedef_in_lre_questions AS t1
               LEFT OUTER JOIN webset.std_in_lre_questions_answers AS t0 ON t1.silqrefid = t0.silqrefid AND t0.stdrefid = " . $tsRefID . "
         WHERE (recdeactivationdt IS NULL OR NOW()< recdeactivationdt)
		   AND COALESCE(set_id, " . $set_id . ") = " . $set_id . "
         ORDER BY t1.silqseq, t1.silqdesc
	";

	$list->addColumn('Question');
	$list->addColumn('Answer')->dataCallback('checkAnswer');

	$list->editURL = 'javascript:api.goto("' . CoreUtils::getURL('questions_add.php', array('dskey' => $dskey, 'RefID' => 'AF_REFID', 'QuestionID' => 'AF_COL4')) . '");';

	$list->deleteTableName = 'webset.std_in_lre_questions_answers';
	$list->deleteKeyField = 'silqarefid';

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

	function checkAnswer($data, $col) {
		if ($data['silqarefid'] > 0) {
			return $data['answer'] . $data['qarejectiondesc'];
		} else {
			return UIMessage::factory('NOT ANSWERED - IMPORTANT!!!')
					->toHTML();
		}
	}

?>