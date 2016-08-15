<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$set_ini = IDEAFormat::getIniOptions();
	

	$list = new ListClass();

	$list->title = $set_ini["in_general_education_considerations"];

	$list->SQL = "
		SELECT conrefid, 
		       '<b>' || CASE other != '' WHEN TRUE THEN other || '</b> ' ELSE COALESCE(macdesc, '')  || ':</b> ' || webset.statedef_mod_acc.stsdesc END, 
			   narr
          FROM webset.std_in_ed_consid
               INNER JOIN webset.statedef_mod_acc ON webset.std_in_ed_consid.progrefid = webset.statedef_mod_acc.stsrefid
               LEFT OUTER JOIN webset.statedef_mod_acc_cat ON webset.statedef_mod_acc_cat.macrefid = webset.statedef_mod_acc.macrefid
         WHERE webset.statedef_mod_acc.screfid = " . VNDState::factory()->id. "
           AND LOWER(modaccommodationsw) = 'y'
           AND webset.std_in_ed_consid.stdrefid = " . $tsRefID . "
         ORDER BY 2, 3
	";

	$list->addColumn('Program Modifications and Accomodations');
	$list->addColumn('Narrative');

	$list->addURL = CoreUtils::getURL('gened_consid_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('gened_consid_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_ed_consid';
	$list->deleteKeyField = 'conrefid';

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