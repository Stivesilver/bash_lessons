<?php

	Security::init();	
	
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	
	$list = new listClass();

	$list->title = 'Discipline Policy and Rules';
	
	$list->SQL = "
		SELECT t0.sdarefid,
			   t1.dprqtext,
			   t0.sdansw,
			   t0.dprqrefid
		  FROM webset.std_dpr_answ AS t0
			   INNER JOIN webset.disdef_disciplinepolicy_rules_quest AS t1 ON t1.dprqrefid = t0.dprqrefid
		 WHERE t0.stdrefid = " . $tsRefID . "
		 ORDER BY seqnum
	";

	$list->addColumn('Discipline Policy and Rules ');
	$list->addColumn('Answer' );
	
	$list->getButton(ListClassButton::ADD_NEW)
        ->disabled(
			db::execSQL("
				SELECT count(1)
				  FROM webset.disdef_disciplinepolicy_rules_quest dis
			     WHERE vndrefid = VNDREFID
			 	   AND dprqrefid NOT IN (SELECT std.dprqrefid
										   FROM webset.std_dpr_answ std
									      WHERE stdrefid = " . $tsRefID . ")
			")->getOne() == '0');

	$list->addURL = CoreUtils::getURL('dpr_quest_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('dpr_quest_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_dpr_answ';
    $list->deleteKeyField = 'sdarefid';

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