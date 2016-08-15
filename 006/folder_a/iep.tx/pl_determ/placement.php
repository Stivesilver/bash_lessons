<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Instructional Arrangement';

	$list->SQL = "
		SELECT std.refid,
			   COALESCE(vouname,school_camp),
			   spccode || ' - ' || spcdesc,
			   ppcd.validvalue || ' - ' || ppcd.validvalueid,
			   spc.validvalue,
			   crtdesc,
			   period_dt
		  FROM webset_tx.std_instruct_arrange std
			   INNER JOIN webset.statedef_placementcategorycode plc ON plc.spcrefid = std.placement
			   LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.campus_id
			   LEFT OUTER JOIN webset.disdef_location loc ON loc.crtrefid = std.location
			   LEFT OUTER JOIN webset.glb_validvalues ppcd ON ppcd.refid = std.ppcdind
			   LEFT OUTER JOIN webset.glb_validvalues spc ON spc.refid = std.speechind
		 WHERE std_refid = " . $tsRefID . "
		 ORDER BY COALESCE(std.period_dt, std.lastupdate)
    ";

	$list->addColumn('Campus');
	$list->addColumn('Instructional Arrangement');
	$list->addColumn('SLC');
	$list->addColumn('Speech Indicator');
	$list->addColumn('Location');
	$list->addColumn('Date')->type('date');

	$list->addURL = CoreUtils::getURL('placement_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('placement_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_instruct_arrange';
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

	print UIMessage::factory('If appropriate, complete the ARD/IEP Supplement for Out-of-District Placement Verification or the Referral to a Regional Day School Program for the Deaf.', UIMessage::NOTE)->toHTML();

?>
