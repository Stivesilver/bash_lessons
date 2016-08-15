<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Benchmark Measurements Templates';

	$list->showSearchFields = true;

	$list->SQL = "
        SELECT it.mirefid,
			   it.cat_id,
			   it.name AS name,
			   it.description AS description,
			   it.rows AS rows,
			   it.order_num AS order_num,
			   ct.name AS catname,
			   it.max_points,
			   'Rows: ' || (SELECT count(1)
			      				   FROM webset.disdef_bgb_measure_rows
				                  WHERE temp_id = mirefid) as crows,
			   it.lastuser AS lastuser,
			   it.lastupdate AS lastupdate,
			   CASE WHEN NOW() > it.end_date THEN 'N' ELSE 'Y' END  as status
	      FROM webset.disdef_bgb_measure_items AS it
	      	   INNER JOIN webset.disdef_bgb_measure_cat AS ct ON (ct.mcrefid = it.cat_id)
         ORDER BY ct.order_num, ct.name, it.order_num
    ";

	$list->addSearchField(FFSelect::factory('Category')
		->sql("
			SELECT mcrefid,
				   name
			  FROM webset.disdef_bgb_measure_cat
		   	 WHERE vndrefid = VNDREFID
			 ORDER BY order_num
		")
	)->sqlField('cat_id');

	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > it.end_date THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Category')->sqlField('catname')->type('group');
	$list->addColumn('Order #')->sqlField('order_num');
	$list->addColumn('Name')->sqlField('name');
	$list->addColumn('Description')->sqlField('description');
	$list->addColumn('Max Value')->sqlField('max_points');
	$list->addColumn('Rows')->sqlField('crows');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('date');
	$list->addColumn('Active')->type('switch')->sqlField('status');

	$list->addURL = 'tmpl_add.php';
	$list->editURL = 'tmpl_add.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.disdef_bgb_measure_items')
			->setNesting('webset.disdef_bgb_measure_rows', 'mrrefid', 'temp_id', 'webset.disdef_bgb_measure_items', 'mirefid')
			->setKeyField('mirefid')
			->applyListClassMode()
	);

	$list->printList();
?>
