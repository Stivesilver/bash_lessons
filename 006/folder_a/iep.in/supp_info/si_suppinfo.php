<?php

	Security::init();	
	
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	
	$bNames = IDEAFormat::getDocBlocks();

	$list = new listClass();
	
	$list->SQL = "
		SELECT sirefid,
			   sicdesc,
			   sinarr
		  FROM webset.std_in_supp_inf
		   	   INNER JOIN webset.statedef_supp_inf_cat ON webset.statedef_supp_inf_cat.sicrefid = webset.std_in_supp_inf.sicrefid
		 WHERE webset.std_in_supp_inf.stdrefid = " . $tsRefID . "
		 ORDER BY sirefid
	";

	$list->title = $bNames[7]['iepdesc'] . "<br> All six areas must be addressed";

	$list->addColumn("Category");
	$list->addColumn("Narrative" );
	
	$list->getButton(ListClassButton::ADD_NEW)
        ->disabled(
			db::execSQL("
				SELECT count(1)
				  FROM webset.statedef_supp_inf_cat state
			     WHERE screfid = " . VNDState::factory()->id . "
			 	   AND sicrefid NOT IN (SELECT std.sicrefid
										  FROM webset.std_in_supp_inf std
										       INNER JOIN webset.statedef_supp_inf_cat ON state.sicrefid = std.sicrefid
									     WHERE stdrefid = " . $tsRefID . ")
			")->getOne() == '0');

	$list->addURL = CoreUtils::getURL('si_suppinfo_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('si_suppinfo_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_in_supp_inf';
    $list->deleteKeyField = 'sirefid';

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