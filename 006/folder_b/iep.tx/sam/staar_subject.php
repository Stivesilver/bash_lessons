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

	$list->title = $assess;

	$list->SQL = "
		SELECT refid,
			   plpgsql_recs_to_str('
				  SELECT CASE WHEN LOWER(swadesc) LIKE ''%other%'' THEN swadesc || '': '' || ''' || COALESCE(other, '') || ''' ELSE swadesc END AS column
					FROM webset.statedef_assess_state
		           WHERE swarefid in (' || assessments || ')', ', '),

			   CASE WHEN LOWER(aaadesc) LIKE '%other%' THEN subject_oth ELSE aaadesc END,

			   plpgsql_recs_to_str('
			       SELECT cast (adesc as varchar) AS column
		             FROM webset.statedef_prim_lang
		            WHERE refid in (' || languages || ')', ', '),

			   plpgsql_recs_to_str('
			       SELECT cast (gldesc as varchar) AS column
		             FROM webset.def_gradelevel
		            WHERE glrefid in (' || grades || ')', ', '),


               plpgsql_recs_to_str ('
					  SELECT CAST(stsdesc as varchar) as column
                        FROM webset.statedef_mod_acc
                       WHERE stsrefid in (' || COALESCE(ids_accommodations,'-1')  || ')
                       ORDER BY stsseq, stsdesc', ', ') || COALESCE(' ' || accomodation, '')

		  FROM webset_tx.std_sam_taks std
			   INNER JOIN webset.statedef_assess_acc ON CAST(aaarefid as varchar) = subjects
		 WHERE stdrefid = " . $tsRefID . "
		   AND samrefid = " . $samrefid . "
		   AND (plpgsql_recs_to_str ('SELECT swadesc AS column
		  FROM webset.statedef_assess_state
		 WHERE swarefid in (' || COALESCE(assessments,'0') || ')', ', ') LIKE '%" . $assess . "%')
		 ORDER BY refid desc
    ";

	$list->addColumn('Assessment');
	$list->addColumn('Subject');
	$list->addColumn('Language');
	$list->addColumn('Grade');
	$list->addColumn('Accommodations or Complexity Level');

	$list->addURL = CoreUtils::getURL('staar_subject_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$list->editURL = CoreUtils::getURL('staar_subject_add.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

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
