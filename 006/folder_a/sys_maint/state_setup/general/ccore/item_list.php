<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Core Standards Items';

	$list->SQL = "
		SELECT itrefid,
			   cat.srefid,
			   subcat.catrefid,
			   item.subrefid,
			   item.name AS itname,
			   subj.name AS subjname,
			   subcat.name AS subcatname,
			   cat.name AS catname,
			   item.description AS description,
			   item.code
		  FROM webset.statedef_ccore_items AS item
			   LEFT JOIN webset.statedef_ccore_subcat AS subcat ON (subcat.subrefid = item.subrefid)
			   LEFT JOIN webset.statedef_ccore_cat AS cat ON (subcat.catrefid = cat.catrefid)
			   LEFT JOIN webset.statedef_ccore_subj AS subj ON (subj.srefid = cat.srefid)
		 WHERE screfid = $staterefid
		 ORDER BY subjname, catname, subcatname, itname
	";

	$list->showSearchFields = true;

	$list->addSearchField("ID", "(itrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory())
		->caption('Subject')
		->sqlField('subj.srefid')
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
		->name('subcat')
		->tie('cat');

	$list->addSearchField(FFSelect::factory())
		->caption('Sub Category')
		->sqlField('item.subrefid')
		->sql('
			SELECT subcat.subrefid, subcat.name
			  FROM webset.statedef_ccore_subcat AS subcat
			 WHERE catrefid = VALUE_01
		')
		->tie('subcat');

	$list->addSearchField('Name', 'item.name', 'text')
		->width(240);

	$list->addSearchField('Code', 'item.code', 'text');

	$list->addColumn('Subject', '22', 'group')
		->sqlField('subjname');

	$list->addColumn('ID')->sqlField('itrefid');
	$list->addColumn('Category')
		->sqlField('catname');

	$list->addColumn('Sub Category')
		->sqlField('subcatname');

	$list->addColumn('Name')->sqlField('itname');
	$list->addColumn('Code')->sqlField('code');
	$list->addColumn('Description')->sqlField('description');

	$list->deleteTableName = 'webset.statedef_ccore_items';
	$list->deleteKeyField = 'itrefid';

	$list->editURL = 'item_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_ccore_items')
			->setKeyField('itrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
