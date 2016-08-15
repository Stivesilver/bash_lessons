<?php
	Security::init();

	$temp_id = io::geti('temp_id');

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->title = 'Add/Edit Benchmark Measurements Rows';

	$edit->setSourceTable('webset.disdef_bgb_measure_rows', 'mrrefid');

	$edit->addGroup('General Information');

	$edit->addControl('', 'hidden')
		->value($temp_id)
		->sqlField('temp_id');

	$edit->addControl("Order #", "INTEGER")
		->name('order_num')
		->value(db::execSQL("
			SELECT MAX(order_num)
			  FROM webset.disdef_bgb_measure_rows
			 WHERE temp_id = $temp_id
		")->getOne() + 10)
		->sqlField('order_num');

	$edit->addControl('Name')->sqlField('name')->css("width", "80%")->req();
	$edit->addControl('Default Value', 'textarea')->sqlField('default_value')->css("width", "80%");
	$edit->addControl('Deactivation Date', 'date')->sqlField('end_date');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL('./tmpl_add.php', array('RefID' => $temp_id));
	$edit->cancelURL = CoreUtils::getURL('./tmpl_add.php', array('RefID' => $temp_id));

	$edit->printEdit();
?>
