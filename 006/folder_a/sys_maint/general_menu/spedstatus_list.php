<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT stsrefid,
		       stsdesc,
		       active_sw,
		       stddmg_active_sw
		  FROM webset.def_spedstatus
		 WHERE (1=1) ADD_SEARCH
		 ORDER BY stsrefid
	";

	$list->title = "Special Education Student Status";
	$list->addSearchField("ID", "(stsrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addColumn('ID')->sqlField('stsrefid');
	$list->addColumn("Student Status")->sqlField('stsdesc');
	$list->addColumn("Special Education Active")->sqlField('active_sw');
	$list->addColumn("Student Demographics Active")->sqlField('stddmg_active_sw');

	$list->addURL = CoreUtils::getURL('./spedstatus_edit.php');
	$list->editURL = CoreUtils::getURL('./spedstatus_edit.php');

	$list->deleteTableName = "webset.def_spedstatus";
	$list->deleteKeyField = "stsrefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_spedstatus')
			->setKeyField('stsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
