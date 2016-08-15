<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Core Standards Subjects';

	$list->SQL = "
		SELECT srefid, name, description
		  FROM webset.statedef_ccore_subj
		 WHERE screfid = $staterefid
	";

	$list->showSearchFields = true;

	$list->addSearchField("ID", "(srefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('Name', 'name', 'text')
		->width(240);

	$list->addColumn('ID')->sqlField('srefid');
	$list->addColumn('Name')->sqlField('name');
	$list->addColumn('Description')->sqlField('description');

	$list->deleteTableName = 'webset.statedef_ccore_subj';
	$list->deleteKeyField = 'srefid';

	$list->editURL = CoreUtils::getURL('./subject_edit.php', array('staterefid' => $staterefid));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_ccore_subj')
			->setKeyField('srefid')
			->applyListClassMode()
			->setNesting('webset.statedef_ccore_cat', 'catrefid', 'srefid', 'webset.statedef_ccore_subj', 'srefid')
			->setNesting('webset.statedef_ccore_subcat', 'subrefid', 'catrefid', 'webset.statedef_ccore_cat', 'catrefid')
			->setNesting('webset.statedef_ccore_items', 'itrefid', 'subrefid', 'webset.statedef_ccore_subcat', 'subrefid')
	);

	$list->printList();
?>
