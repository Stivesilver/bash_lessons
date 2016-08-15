<?php

	Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Transition Services';

	$list->SQL = "
		SELECT refid,
			   plpgsql_recs_to_str('
			       SELECT gdssdesc AS column
					 FROM webset.disdef_bgb_goaldomainscope
					WHERE gdsrefid in (' || COALESCE(std.scope, '0') || ')
					ORDER BY gdssdesc ASC
			   ', ', ') || COALESCE(': ' || std.otherprovider, '') AS gdssdesc,
			   transervice || COALESCE(' ' || otheractivitiy, '') AS transerv,
			   plpgsql_recs_to_str('
			       SELECT validvalue AS column
					 FROM webset.disdef_validvalues
					WHERE refid in (' || COALESCE(provider, '0') || ')
					ORDER BY sequence_number, validvalue ASC
			   ', ', ') || COALESCE(': ' || std.otherprovider, '') AS provider,
			   dateend
		  FROM webset.std_nts_activities std
			   INNER JOIN  webset.statedef_nts_activities state ON state.acrefid = std.acrefid
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY sequence, transervice
	";

	$list->addColumn('Area')->sqlField('gdssdesc');
	$list->addColumn('The school will:')->sqlField('transerv');
	$list->addColumn('Agency/Person Responsible')->sqlField('provider');
	$list->addColumn('Completion Date')->type('date')->sqlField('dateend');

	$list->addURL = CoreUtils::getURL('srv_transition_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_transition_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_nts_activities';
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
