<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

    $list = new ListClass();

    $list->title = 'Extent of Participation';

    $list->SQL = "
        SELECT pperefid, 
               ppedtext, 
               epdnarrtext,
               plpgsql_recs_to_str ('SELECT cast (code || ''. '' || indicator as varchar)  AS column 
                                       FROM webset.statedef_part_ext_indicator 
                                      WHERE indrefid in (' || CASE WHEN indicators is NULL THEN '0' WHEN indicators='' THEN '0' ELSE indicators END  || ') 
                                      ORDER BY code, indicator', '<br> ') as indicators
          FROM webset.statedef_part_ext state
               INNER JOIN webset.std_part_ext std ON state.ppedrefid = std.ppedrefid 
         WHERE stdrefid=" . $tsRefID . " 
         ORDER BY pperefid
    ";

    $list->addColumn('Student Extent of Participation');
    $list->addColumn('Narrative');
    if (IDEACore::disParam(76) == 'Y') $list->addColumn('Indicators');

    $list->addURL = CoreUtils::getURL('srv_part_ext_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_part_ext_add.php', array('dskey' => $dskey));

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled(db::execSQL($list->SQL)->getOne() > 0);

    $list->deleteTableName = 'webset.std_part_ext';
    $list->deleteKeyField = 'pperefid';

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