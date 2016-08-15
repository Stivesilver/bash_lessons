<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');	
	
	$area_id = 136;

	$list = new listClass();

	$list->title = 'Courses of Study';

	$list->SQL = "
		SELECT std.refid,
		       dsydesc,
			   grade.validvalue,
			   txt01,
			   txt02
		  FROM webset.std_general std
			   INNER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = std.int01
			   INNER JOIN webset.glb_validvalues grade ON grade.refid = std.int02
		 WHERE stdrefid = " . $tsRefID . " 
		   AND area_id = " . $area_id . "
		 ORDER BY dsybgdt ASC
	";

	$list->addColumn('School Year');
	$list->addColumn('Grade Level');
	$list->addColumn('Courses');
	$list->addColumn('Credits Earned');

	$list->addURL = CoreUtils::getURL('cs_courses_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('cs_courses_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
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