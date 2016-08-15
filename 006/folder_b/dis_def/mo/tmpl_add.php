<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->title = 'Add/Edit Benchmark Measurements Template';

	$edit->setSourceTable('webset.disdef_bgb_measure_items', 'mirefid');

	$edit->addGroup('General Information');

	$edit->addControl("Order #", "INTEGER")
		->name('order_num')
		->value(db::execSQL("
			SELECT MAX(order_num)
			  FROM webset.disdef_bgb_measure_items
		")->getOne() + 10)
		->sqlField('order_num');

	$edit->addControl(FFSelect::factory('Category')
		->sql("
			SELECT mcrefid,
				   name
			  FROM webset.disdef_bgb_measure_cat
		   	 WHERE vndrefid = VNDREFID
			 ORDER BY order_num
		")
	)->sqlField('cat_id');

	$edit->addControl('Name')->sqlField('name')->css("width", "80%")->req();
	$edit->addControl('Description', 'textarea')->sqlField('description')->css("width", "80%");
	$edit->addControl('Deactivation Date', 'date')->sqlField('end_date');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->saveAndEdit = true;

	$edit->finishURL = 'tmpl_list.php';
	$edit->cancelURL = 'tmpl_list.php';

	$edit->printEdit();

	$list = new ListClass();

	$list->setMasterRecordID($RefID);

	$list->title = 'Benchmark Measurements Rows';

	$list->showSearchFields = true;

	$list->SQL = "
        SELECT rw.mrrefid,
			   rw.name AS name,
			   rw.default_value,
			   rw.order_num AS order_num,
			   it.name AS itname,
			   rw.lastuser AS lastuser,
			   rw.lastupdate AS lastupdate,
			   CASE WHEN NOW() > rw.end_date THEN 'N' ELSE 'Y' END  as status
	      FROM webset.disdef_bgb_measure_rows AS rw
	      	   INNER JOIN webset.disdef_bgb_measure_items AS it ON (rw.temp_id = it.mirefid)
	      WHERE temp_id = $RefID
         ORDER BY rw.order_num
    ";

	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > rw.end_date THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Order #')->sqlField('order_num');
	$list->addColumn('Name')->sqlField('name');
	$list->addColumn('Default Value')->sqlField('default_value');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('date');
	$list->addColumn('Active')->type('switch')->sqlField('status');

	$list->addURL = CoreUtils::getURL('./tmpl_row_add.php', array('temp_id' => $RefID));
	$list->editURL = CoreUtils::getURL('./tmpl_row_add.php', array('temp_id' => $RefID));

	$list->deleteTableName = 'webset.disdef_bgb_measure_rows';
	$list->deleteKeyField = 'mrrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.disdef_bgb_measure_rows')
			->setKeyField('mrrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
