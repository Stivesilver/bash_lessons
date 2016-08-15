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

	$list->title = $assess . ' Rationale';

	$list->SQL = "
		SELECT t0.refid,
			   t1.validvalue,
			   CASE WHEN t2.validvalue like 'Other%' THEN COALESCE(rationale, '') ELSE COALESCE(t2.validvalue,rationale) END
		  FROM webset_tx.std_sam_taks_ratio AS t0
			   LEFT JOIN webset.glb_validvalues AS t1 ON subject_id = t1.refid
			   LEFT JOIN webset.glb_validvalues AS t2 ON reationale_id = t2.refid
		 WHERE stdrefid = " . $tsRefID . "
		   AND samrefid = " . $samrefid . "
		   AND COALESCE(t2.validvalueid, '" . $assess . "') = '" . $assess . "'
		 ORDER BY t0.lastupdate
    ";

	$list->addColumn('Assessment');
	$list->addColumn('Subject');
	$list->addColumn('Language');
	$list->addColumn('Grade');
	$list->addColumn('Accommodations or Complexity Level');

	$list->addURL = CoreUtils::getURL('staar_rationale_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$list->editURL = CoreUtils::getURL('staar_rationale_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

	$list->deleteTableName = 'webset_tx.std_sam_taks_ratio';
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