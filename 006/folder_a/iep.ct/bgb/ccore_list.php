<?php

	Security::init();

	$list = new ListClass();
	$list->title = 'Core Standards Items';

	$list->SQL = "
		SELECT item.code,
		       cat.srefid,
		       subcat.catrefid,
		       item.subrefid,
		       item.name AS itname,
		       subj.name AS subjname,
		       subcat.name AS subcatname,
		       cat.name AS catname
		  FROM webset.statedef_ccore_items AS item
		       LEFT JOIN webset.statedef_ccore_subcat AS subcat ON (subcat.subrefid = item.subrefid)
		       LEFT JOIN webset.statedef_ccore_cat AS cat ON (subcat.catrefid = cat.catrefid)
		       LEFT JOIN webset.statedef_ccore_subj AS subj ON (subj.srefid = cat.srefid)
		 WHERE subj.screfid = " . VNDState::factory()->id . "
		 ORDER BY subjname, catname, subcatname, itname
	";

	$list->showSearchFields = true;

	$list->addSearchField(FFSelect::factory())
		->caption('Subject')
		->sqlField('subj.srefid')
		->sql("
			SELECT srefid, subj.name
			  FROM webset.statedef_ccore_subj AS subj
			 WHERE subj.screfid = " . VNDState::factory()->id . "
		")
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
		->width(240)
		->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addSearchField('Code', 'item.code', 'text')
		->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('Subject', '22', 'group')
		->sqlField('subjname');

	$list->addColumn('Category')
		->sqlField('catname');

	$list->addColumn('Sub Category')
		->sqlField('subcatname');

	$list->addColumn('Name')->sqlField('itname');
	$list->addColumn('Code')->sqlField('code');

	//	$list->addButton('Process', 'process()')
	//		->width(115);

	$list->hideCheckBoxes = false;

	$list->printList();
?>

<script>
	function process() {
		var list = ListClass.get()
		var selected = list.getSelectedValues().values.join(', ');
		api.window.dispatchEvent('ccore_selected', {'selected' : selected});
		api.window.destroy();
	}
</script>
