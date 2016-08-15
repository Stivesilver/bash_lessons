<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$list       = new ListClass();
	$editUrl    = CoreUtils::getURL('assessments_edit.php', array('dskey' => $dskey));

	$list->addURL          = $editUrl;
	$list->editURL         = $editUrl;
	$list->deleteKeyField  = "shsdrefid";
	$list->deleteTableName = "webset.es_std_scr";
	$list->title           = "Assessment Results";
	$list->pageCount       = "50";

	$list->SQL = "
		SELECT shsdrefid,
               scrdesc,
               COALESCE(test_name, hspdesc),
               screener,
               test_usrtitle,
               to_char(shsddate, 'MM/DD/YYYY'),
               replace(shsdhtmltext, '\n', '<br/>')
          FROM webset.es_std_scr std
               INNER JOIN webset.es_disdef_screeningtype ON screenid = scrrefid
               LEFT OUTER JOIN webset.es_scr_disdef_proc proc ON std.hsprefid = proc.hsprefid
         WHERE iepyear = " . $stdIEPYear . "
         ORDER BY scrseq, scrdesc, shsdrefid
		";


	$list->addColumn("Area Assessed");

	$list->addColumn("Procedure Used");

	$list->addColumn("Evaluator");

	$list->addColumn("Title");

	$list->addColumn("Date");

	$list->addColumn("Results (Strengths & Needs)");

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