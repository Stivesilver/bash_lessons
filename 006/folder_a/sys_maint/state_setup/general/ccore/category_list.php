<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Core Standards Subjects';

	$list->SQL = "
		SELECT catrefid,
			   cat.srefid,
			   cat.name AS catname,
			   subj.name AS subjname,
			   cat.description AS description
		  FROM webset.statedef_ccore_cat AS cat
		 	   LEFT JOIN webset.statedef_ccore_subj AS subj ON (subj.srefid = cat.srefid)
		 WHERE screfid = $staterefid
		 ORDER BY subjname, catname
	";

	$list->showSearchFields = true;

	$list->addSearchField("ID", "(catrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory())
		->caption('Subject')
		->sqlField('srefid')
		->sql('
			SELECT srefid, subj.name
			  FROM webset.statedef_ccore_subj AS subj
		');

	$list->addSearchField('Name', 'cat.name', 'text')
		->width(240);

	$list->addColumn('Subject', '22', 'group')
		->sqlField('subjname');
	$list->addColumn('ID')->sqlField('catrefid');
	$list->addColumn('Name')->sqlField('catname');
	$list->addColumn('Description')->sqlField('description');

	$list->deleteTableName = 'webset.statedef_ccore_cat';
	$list->deleteKeyField = 'catrefid';

	$list->editURL = 'category_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_ccore_cat')
			->setKeyField('catrefid')
			->applyListClassMode()
			->setNesting('webset.statedef_ccore_subcat', 'subrefid', 'catrefid', 'webset.statedef_ccore_cat', 'catrefid')
			->setNesting('webset.statedef_ccore_items', 'itrefid', 'subrefid', 'webset.statedef_ccore_subcat', 'subrefid')
	);

	$list->printList();
?>
