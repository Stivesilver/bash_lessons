<?php

	Security::init();	
	
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
   
	$list = new ListClass();
	
	$list->title = 'Present Levels of Academic Achievement and Functional Performance';

	$list->SQL = "
			SELECT pglprefid,
				   tsndesc,
				   pglpnarrative,
				   strengths,
				   concerns,
				   impact
			  FROM webset.std_in_pglp
				   LEFT OUTER JOIN webset.disdef_tsn ON webset.disdef_tsn.tsnrefid = webset.std_in_pglp.tsnrefid
			 WHERE stdrefid = " . $tsRefID . "
			   AND iepyear = " . $stdIEPYear . "
		  	 ORDER BY pglpseq, tsnnum
		";

	$list->addColumn("Area");
	$list->addColumn("Description");
	$list->addColumn("Strengths");
	$list->addColumn("Concerns");
	$list->addColumn("Impact");

    $list->addURL = CoreUtils::getURL('plaafp_edit.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('plaafp_edit.php', array('dskey' => $dskey));

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