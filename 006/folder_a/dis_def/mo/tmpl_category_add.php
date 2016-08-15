<?php
    Security::init();
    
    $edit = new editClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit Benchmark Measurements Categories';
        
    $edit->setSourceTable('webset.disdef_bgb_measure_cat', 'mcrefid');
        
    $edit->addGroup('General Information');

	$edit->addControl("Order #", "INTEGER")
		->name('order_num')
		->sqlField('order_num')
		->value(db::execSQL("
			SELECT MAX(order_num)
			  FROM webset.disdef_bgb_measure_cat
			 WHERE vndrefid = VNDREFID
		")->getOne() + 10);

    $edit->addControl('Category Name')->sqlField('name')->css("width", "80%")->req();    
    $edit->addControl('Deactivation Date', 'date')->sqlField('end_date');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

    $edit->finishURL = 'tmpl_category_list.php';
    $edit->cancelURL = 'tmpl_category_list.php';

    $edit->printEdit();
    
?>
