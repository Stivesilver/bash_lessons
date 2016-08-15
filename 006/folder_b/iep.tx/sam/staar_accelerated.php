<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudentTX::factory($tsRefID);

	$list = new ListClass();

	$list->title = $assess . ' Accelerated Instruction';

	$list->SQL = "
		SELECT t0.refid,
			   CASE WHEN swadesc = 'STAAR Other' THEN COALESCE(assessment_oth, swadesc) ELSE swadesc END,
               CASE WHEN aaadesc = 'Other' THEN subject_oth ELSE aaadesc END,
			   CASE WHEN grade_id = '0' THEN grade_oth ELSE gldesc END,
			   plan1,
			   plan2,
			   plan3
		  FROM webset_tx.std_sam_taks_accelerate AS t0
         	   INNER JOIN webset.statedef_assess_state AS t1 ON assessment_id::integer = t1.swarefid
               INNER JOIN webset.statedef_assess_acc AS t3 ON subject_id::integer = aaarefid
		   	   LEFT OUTER JOIN webset.def_gradelevel AS t2 ON grade_id::integer = glrefid
		 WHERE stdrefid = " . $tsRefID . "
		   AND samrefid = " . $samrefid . "
		   AND swadesc LIKE '%" . $assess . "%'
		 ORDER BY t0.lastupdate
    ";

	$list->addColumn('Assessment');
	$list->addColumn('Subject');
	$list->addColumn('Grade');
	$list->addColumn('Administration 1');
	$list->addColumn('Administration 2');
	$list->addColumn('Administration 3');

	$list->addURL = CoreUtils::getURL('staar_accelerated_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$list->editURL = CoreUtils::getURL('staar_accelerated_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

	$list->deleteTableName = 'webset_tx.std_sam_taks_accelerate';
	$list->deleteKeyField = 'refid';

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
