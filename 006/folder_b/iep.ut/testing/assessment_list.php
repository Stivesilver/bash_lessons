<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Participation in Statewide and District/Schoolwide Assessment Programs';

	$list->SQL = "
		SELECT sswarefid,
			   CASE WHEN LOWER(swadesc) LIKE '%other%' THEN swadesc || ': ' || COALESCE(other, '') ELSE swadesc END,
			   array_to_string(
		           ARRAY(
		            SELECT aaadesc
		              FROM webset.statedef_assess_acc AS s
		             WHERE ',' || subjects || ',' LIKE '%,' || aaarefid::varchar || ',%'
		           ),
		           ', '
		       ) AS subj,
			   gldesc ,
			   partcode,
			   na_reason
		  FROM webset.std_assess_state std
			   INNER JOIN webset.def_gradelevel gl ON CAST(gl.glrefid AS VARCHAR) = grades
			   INNER JOIN webset.statedef_assess_state asm ON CAST(asm.swarefid AS VARCHAR) = assessments
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		 ORDER BY sswarefid DESC
    ";

	$list->addColumn('Assessment');
	$list->addColumn('Subject')->sqlField('subj');
	$list->addColumn('Grade');
	$list->addColumn('Participation');
	$list->addColumn('Comments');

	$list->addURL = CoreUtils::getURL('assessment_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('assessment_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_assess_state';
	$list->deleteKeyField = 'sswarefid';

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
