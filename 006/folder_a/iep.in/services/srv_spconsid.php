<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'Special Factors to Consider';

	$list->SQL = "
        SELECT COALESCE(t0.sscmrefid, 0) as sscmrefid,
               t1.scmquestion,
               CASE LOWER(SUBSTRING(scanswer FROM '..')) WHEN 'ye' THEN 'Y' WHEN 'no' THEN 'N' END,
               t1.scmrefid
          FROM webset.statedef_spconsid_quest AS t1
               LEFT OUTER JOIN webset.std_spconsid AS t0 ON t1.scmrefid = t0.scqrefid AND t0.stdrefid = " . $tsRefID . "
			   LEFT OUTER JOIN webset.statedef_spconsid_answ t2  ON t0.scarefid = t2.scarefid
         WHERE (t1.recdeactivationdt IS NULL OR NOW()< t1.recdeactivationdt)
		   AND t1.screfid = " . VNDState::factory()->id . "
         ORDER BY t1.seqnum, t1.scmquestion
	";

	$list->addColumn('Question');
	$list->addColumn('Answer')->type('switch');

	$list->editURL = 'javascript:api.goto("' . CoreUtils::getURL('srv_spconsid_add.php', array('dskey' => $dskey, 'RefID' => 'AF_REFID', 'QuestionID' => 'AF_COL3')) . '");';

	$list->deleteTableName = 'webset.std_spconsid';
	$list->deleteKeyField = 'sscmrefid';

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