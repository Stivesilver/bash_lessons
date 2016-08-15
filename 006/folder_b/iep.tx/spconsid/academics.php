<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Academics/Preacademics';

	$list->SQL = "
		SELECT refid,
			   CASE ac_desc WHEN 'Other' THEN 'Other: ' || COALESCE(area_other,'') ELSE ac_desc END ,
			   skill_level,
			   strengths,
			   needs
		  FROM webset_tx.std_academics INNER JOIN webset_tx.def_academics USING (ac_refid)
		 WHERE (1=1) ADD_SEARCH
		   AND std_refid = " . $tsRefID . "
		   AND iep_year = " . $stdIEPYear . "
		 ORDER BY seqnum, ac_desc
	";

	$list->addColumn('Instructional Area');
	$list->addColumn('Skill Level');
	$list->addColumn('Strengths');
	$list->addColumn('Needs');

	$list->addURL = CoreUtils::getURL('academics_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('academics_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_academics';
	$list->deleteKeyField = 'refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();
	
	db::execSQL("
		UPDATE webset.std_spconsid SET saveapp = 'Y' WHERE sscmrefid = " . io::geti('spconsid'). "
	");
?>