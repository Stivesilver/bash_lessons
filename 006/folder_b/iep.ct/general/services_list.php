<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$esy = io::get('esy');

	$list = new listClass();

	$list->multipleEdit = "no";

	$list->SQL = "
		SELECT ns.refid,
		       typedesc,
		       CASE SUBSTRING(lower(nsdesc) FROM 'other')
		       WHEN 'other' THEN tnsoth
		       ELSE nsdesc || COALESCE('. ' || tnsoth, '')
		       END AS nsdesc,
		       TO_CHAR(ns.begdate, 'MM-DD-YYYY') AS bdate,
		       TO_CHAR(ns.enddate, 'MM-DD-YYYY') AS edate,
		       um_title,
		       inarr,
		       frequency_text,
		       COALESCE(umlastname, '') || ', ' || COALESCE(umfirstname, '') AS implname,
		       CASE SUBSTRING(lower(crtdesc) FROM 'other')
		       WHEN 'other' THEN locoth
		       ELSE crtdesc
		       END AS loc
		  FROM webset.std_oh_ns AS ns
		       INNER JOIN webset.disdef_oh_ns ON webset.disdef_oh_ns.refid = tnsrefid
		       INNER JOIN webset.statedef_services_type AS t3 ON t3.trefid = webset.disdef_oh_ns.servicetype
		       LEFT JOIN webset.disdef_location AS loc ON (ns.locid = loc.crtrefid)
		       LEFT OUTER JOIN public.sys_usermst ON ns.umrefid = public.sys_usermst.umrefid
		 WHERE ns.stdrefid = " . $tsRefID . "
		   AND iepyear = $stdIEPYear
		   AND esy = '" . $esy . "'
		 ORDER BY t3.seqnum, typedesc, ns.refid
    ";

	if ($esy == "Y") {
		$list->title = "ESY Services";
	} else {
		$list->title = "Services";
	}

	$list->addColumn("Type", "", "group")->sqlField('typedesc');
	$list->addColumn("Service")->sqlField('nsdesc');
	$list->addColumn("Frequency")->sqlField('frequency_text');
	$list->addColumn("Provider Title")->sqlField('um_title');
	$list->addColumn("Service Implementer")->sqlField('implname');
	$list->addColumn("Implementor Title")->sqlField('inarr');
	$list->addColumn("Initiation Date")->sqlField('bdate');
	$list->addColumn("Ending Date")->sqlField('edate');
	$list->addColumn("Site")->sqlField('loc');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_oh_ns')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addURL = CoreUtils::getURL('services_edit.php', array('dskey' => $dskey, 'esy' => $esy));
	$list->editURL = CoreUtils::getURL('services_edit.php', array('dskey' => $dskey, 'esy' => $esy));

	$list->deleteTableName = "webset.std_oh_ns";
	$list->deleteKeyField  = "refid";

	$list->printList();
?>
