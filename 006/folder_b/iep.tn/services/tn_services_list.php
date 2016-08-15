<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Services';

	$list->SQL = "
		SELECT stn.stn_refid,

		       CASE SUBSTRING(lower(dn.nsdesc) FROM 'other')
		       	WHEN 'other' THEN stn.stn_other
		       	ELSE dn.nsdesc || COALESCE('. ' || stn.stn_other, '')
		       END AS nsdesc,

			   stn.stn_provider,
		       stn.stn_begdate,
		       stn.stn_enddate,
			   stn.stn_required_sw,
			   stn.stn_payor,
			   stn.stn_revdate,
			   glb.validvalue AS status,

			   crt.crtdesc,
			   sf.sfdesc,
			   int.validvalue
		  FROM webset.std_tn_ns AS stn
		       INNER JOIN webset.disdef_oh_ns AS dn ON dn.refid = stn.serv_refid
		       LEFT JOIN webset.disdef_location AS crt ON crt.crtrefid = stn.crtrefid
		       LEFT JOIN webset.disdef_frequency AS sf ON sf.sfrefid = stn.sfrefid
		       LEFT JOIN webset.disdef_validvalues AS int ON int.refid = stn.int_refid
		       LEFT JOIN webset.glb_validvalues AS glb ON glb.refid = stn.revs_refid
		 WHERE stn.stdrefid = " . $tsRefID . "
		   AND stn.iepyear = " . $stdIEPYear . "
    ";

	$list->addColumn('Service')
		->sqlField('nsdesc');

	$list->addColumn('Provider')
		->sqlField('stn_provider');

	$list->addColumn('Required')
		->type(ListClassColumn::TYPE_SWITCH)
		->sqlField('stn_required_sw')
		->width('1%')
		->align('center');

	$list->addColumn('Starting Date')
		->type('date')
		->sqlField('stn_begdate')
		->width('1%');

	$list->addColumn('Expected Duration')
		->type('date')
		->sqlField('stn_enddate')
		->width('1%');

	$list->setColumnsGroup('Mehtod');

	$list->addColumn('Environment')
		->sqlField('crtdesc')
		->width('10%');

	$list->addColumn('Frequency')
		->sqlField('sfdesc')
		->width('10%');

	$list->addColumn('Intensity')
		->sqlField('validvalue')
		->width('10%');

	$list->setColumnsGroup('');

	$list->addColumn('Payor')
		->sqlField('stn_payor');

	$list->addColumn('Review Date')
		->type('date')
		->sqlField('stn_revdate')
		->width('1%');

	$list->addColumn('Review Status')
		->sqlField('status');

	$list->deleteTableName = 'webset.std_tn_ns';
	$list->deleteKeyField  = 'stn_refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(IDEAAppArea::TN_IFSP_SEVICES)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addURL = CoreUtils::getURL('./tn_services_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./tn_services_edit.php', array('dskey' => $dskey));


	$list->printList();
?>