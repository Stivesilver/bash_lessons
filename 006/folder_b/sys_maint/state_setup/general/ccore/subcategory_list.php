<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Core Standards Subjects';

	$list->SQL = "
		SELECT subrefid,
			   cat.srefid,
			   subj.name AS subjname,
			   cat.name AS catname,
			   subcat.catrefid,
			   subcat.name AS subcatname,
			   subcat.description AS description
		  FROM webset.statedef_ccore_subcat AS subcat
		       LEFT JOIN webset.statedef_ccore_cat AS cat ON (subcat.catrefid = cat.catrefid)
		  	   LEFT JOIN webset.statedef_ccore_subj AS subj ON (subj.srefid = cat.srefid)
		 WHERE screfid = $staterefid
		 ORDER BY subjname, catname, subcatname
	";

	$list->showSearchFields = true;

	$list->addSearchField("ID", "(subrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory())
		->caption('Subject')
		->sqlField('srefid')
		->sql('
			SELECT srefid, subj.name
			  FROM webset.statedef_ccore_subj AS subj
		')
		->name('cat');

	$list->addSearchField(FFSelect::factory())
		->caption('Category')
		->sqlField('subcat.catrefid')
		->sql('
			SELECT catrefid, cat.name
			  FROM webset.statedef_ccore_cat AS cat
			 WHERE srefid = VALUE_01
		')
		->tie('cat');

	$list->addSearchField('Name', 'subcat.name', 'text')
		->width(240);

	$list->addColumn('Subject', '22', 'group')
		->sqlField('subjname');

	$list->addColumn('ID')->sqlField('subrefid');
	$list->addColumn('Category')
		->sqlField('catname');

	$list->addColumn('Name')->sqlField('subcatname');
	$list->addColumn('Description')->sqlField('description');

	$list->deleteTableName = 'webset.statedef_ccore_subcat';
	$list->deleteKeyField = 'subrefid';

	$list->editURL = 'subcategory_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_ccore_subcat')
			->setKeyField('subrefid')
			->applyListClassMode()
			->setNesting('webset.statedef_ccore_items', 'itrefid', 'subrefid', 'webset.statedef_ccore_subcat', 'subrefid')
	);

	$list->printList();
?>
