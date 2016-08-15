<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'Benchmark Measurements Categories';

    $list->showSearchFields = true;

    $list->SQL = "
        SELECT mcrefid,
			   order_num,
			   name,
			   'Templates: ' || (SELECT count(1)
			      				   FROM webset.disdef_bgb_measure_items
				                  WHERE cat_id = mcrefid) as templates,
			   lastuser,
			   lastupdate,
			   CASE WHEN NOW() > end_date THEN 'N' ELSE 'Y' END  as status
	      FROM webset.disdef_bgb_measure_cat
	     WHERE vndrefid = VNDREFID
         ORDER BY order_num, name
    ";

    $list->addSearchField(
        FFIDEAStatus::factory()
            ->sqlField("CASE WHEN NOW() > end_date THEN 'N' ELSE 'Y' END")
    );

	$list->addColumn('Order #')->sqlField('order_num');
    $list->addColumn('Category Name')->sqlField('name');
    $list->addColumn('Templates')->sqlField('templates');
    $list->addColumn('Last User')->sqlField('lastuser');
    $list->addColumn('Last Update')->sqlField('lastupdate')->type('date');
    $list->addColumn('Active')->type('switch');

    $list->addURL = 'tmpl_category_add.php';
    $list->editURL = 'tmpl_category_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_bgb_measure_cat')
            ->setKeyField('mcrefid')
			->setNesting('webset.disdef_bgb_measure_items', 'mirefid', 'cat_id', 'webset.disdef_bgb_measure_cat', 'mcrefid')
			->setNesting('webset.disdef_bgb_measure_rows', 'mrrefid', 'temp_id', 'webset.disdef_bgb_measure_items', 'mirefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
