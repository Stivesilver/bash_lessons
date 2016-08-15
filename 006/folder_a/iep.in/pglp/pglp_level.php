<?php

	Security::init();	
	
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
   
	$list = new ListClass();
	
	$list->title = 'Present Grades and Levels of Performance';
	
	if (IDEACore::disParam(15) == 'Y') {
		$list->SQL = "
			SELECT pglprefid,
				   pglplgrade,
				   gldesc,
				   CASE WHEN tsnnum IS NULL THEN '' ELSE '#'||tsnnum||' - ' END||tsndesc,
				   pglpnarrative
			  FROM webset.std_in_pglp
			   	   LEFT OUTER JOIN webset.disdef_gradelevel ON webset.disdef_gradelevel.glrefid = webset.std_in_pglp.glrefid
				   LEFT OUTER JOIN webset.disdef_tsn ON webset.disdef_tsn.tsnrefid = webset.std_in_pglp.tsnrefid
			 WHERE stdrefid = " . $tsRefID . "
			   AND iepyear = " . $stdIEPYear . "
			 ORDER BY pglpseq, tsnnum
		";
	} else {
		$list->SQL = "
			SELECT pglprefid,
				   pglplgrade,
				   gl_code,
				   CASE WHEN tsnnum IS NULL THEN '' ELSE '#'||tsnnum||' - ' END||tsndesc,
				   pglpnarrative
			  FROM webset.std_in_pglp
				   LEFT OUTER JOIN c_manager.def_grade_levels ON c_manager.def_grade_levels.gl_refid = webset.std_in_pglp.gl_refid
				   LEFT OUTER JOIN webset.disdef_tsn ON webset.disdef_tsn.tsnrefid = webset.std_in_pglp.tsnrefid
			 WHERE stdrefid = " . $tsRefID . "
			   AND iepyear = " . $stdIEPYear . "
		  	 ORDER BY pglpseq, tsnnum
		";
	}

	$list->addColumn("Letter Grade")->width('8%');
	$list->addColumn("Grade Equivalent")->width('10%');
	$course_title = IDEACore::disParam(118);
	$course_title = $course_title != "" ? $course_title : "Course";
	$list->addColumn($course_title)->width('20%');
	$list->addColumn("Narrative")->width('62%');

    $list->addURL = CoreUtils::getURL('pglp_level_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('pglp_level_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_in_pglp';
    $list->deleteKeyField = 'pglprefid';

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