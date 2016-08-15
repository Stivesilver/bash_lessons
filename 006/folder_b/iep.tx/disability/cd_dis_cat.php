<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Disability Category';

	$list->SQL = "
		SELECT sdrefid,
			   dccode,
			   dcdesc,
			   validvalue,
			   CASE WHEN LOWER(dcdesc) NOT LIKE '%other%' THEN
			   plpgsql_recs_to_str(
				   'SELECT cast (ad.code || '' - '' || adname as varchar) AS column
		              FROM webset.statedef_disabling_indicatam as am
			               INNER JOIN webset.statedef_disabling_indicatad as ad ON am.amirefid = ad.amirefid
		             WHERE adirefid in (' || COALESCE(CASE WHEN substring(replace(indicators,',','') from chr(92) || 'd{1,}')=replace(indicators,',','') THEN indicators ELSE NULL END,'0') || ' )
		             ORDER BY am.code, ad.code', '<br/>')
			       ELSE indicators END
		  FROM webset.std_disabilitymst std
			   INNER JOIN webset.statedef_disablingcondition dis ON std.dcrefid = dis.dcrefid
			   INNER JOIN webset.glb_validvalues ON validvalueid = CAST(sdtype as varchar) AND valuename = 'TXDisabilityType'
		 WHERE std.stdrefid = " . $tsRefID . "
		 ORDER BY std.sdtype, dis.dccode
    ";

	$list->addColumn('ID');
	$list->addColumn('Disability Category');
	$list->addColumn('Type');
	$list->addColumn('Areas of Qualification');

	$list->addURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_disabilitymst';
	$list->deleteKeyField = 'sdrefid';

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

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Written Information';
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '60%';
	$edit->addControl(
		FFSwitchYN::factory(
			'Parents of students who meet eligibility criteria for visual or auditory impairments or deaf-blindness
			have been given written information, within the past year, about programs offered by the Texas
			School for the Blind and Visually Impaired or Texas School for the Deaf, including eligibility and
			admission requirements and the rights of students related to admission.'
		)	->data(array('Y'=>'Yes', 'N'=>'N/A'))
			->emptyOption(TRUE)
			->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'disability_letter'))
			->breakRow()
			->name('answer')
	);
	$edit->saveURL = CoreUtils::getURL('cd_dis_cat_save.php', array('dskey' => $dskey));
	$edit->printEdit();
?>
