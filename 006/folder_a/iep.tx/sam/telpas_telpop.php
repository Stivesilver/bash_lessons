<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'TELPAS';

	$list->SQL = "
		SELECT refid,
			   plpgsql_recs_to_str('
				   SELECT swadesc AS column
		             FROM webset.statedef_assess_state
		            WHERE swarefid in (' || COALESCE(assessments, '0') || ')', ', '),

			   aaadesc,

			   plpgsql_recs_to_str('
			       SELECT cast (adesc as varchar) AS column
		             FROM webset.statedef_prim_lang
		            WHERE refid in (' || COALESCE(languages, '0') || ')', ', '),

			   plpgsql_recs_to_str('
			       SELECT cast (gldesc as varchar) AS column
		             FROM webset.def_gradelevel
		            WHERE glrefid in (' || COALESCE(grades, '0') || ')', ', '),

			   accomodation
		  FROM webset_tx.std_sam_taks std
			   INNER JOIN webset.statedef_assess_acc ON aaarefid = subjects::integer
		 WHERE stdrefid = " . $tsRefID . "
		   AND samrefid = " . $samrefid . "
		   AND (plpgsql_recs_to_str ('SELECT swadesc AS column
		  FROM webset.statedef_assess_state
		 WHERE swarefid in (' || COALESCE(assessments, '0') || ')', ', ') like '%TELPAS%')
		 ORDER BY refid desc
    ";

	$list->addColumn('Assessment');
	$list->addColumn('Subject');
	$list->addColumn('Language');
	$list->addColumn('Grade');
	$list->addColumn('Accommodations or Complexity Level');

	$list->addURL = CoreUtils::getURL('telpas_telpop_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid));
	$list->editURL = CoreUtils::getURL('telpas_telpop_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid));

	$list->deleteTableName = 'webset_tx.std_sam_taks';
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
