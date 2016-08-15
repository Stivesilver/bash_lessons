<?php

	Security::init();

	$RefID = io::get('RefID');

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit 504 Forms Category';

		$edit->setSourceTable('webset.disdef_fif_form_category', 'fcrefid');

		$edit->addGroup('General Information');
		$edit->addControl('Category')->sqlField('cname')->name('cname')->size(80)->req();
		$edit->addControl('Deactivation Date', 'date')->sqlField('enddate')->name('enddate');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		$edit->addSQLConstraint(
			'Such category already exists', "
            SELECT 1
              FROM webset.disdef_fif_form_category
             WHERE vndrefid = VNDREFID
               AND cname = '[cname]'
               AND fcrefid != AF_REFID
        ");

		$edit->finishURL = 'form_category.php';
		$edit->cancelURL = 'form_category.php';

		$edit->firstCellWidth = '30%';

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = '504 Forms Categories';
		$list->showSearchFields = true;

		$list->SQL = "
            SELECT fcrefid,
                   cname,                   
                   CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
              FROM webset.disdef_fif_form_category                   
             WHERE vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY cname
        ";

		$list->addSearchField(
			FFSwitchAI::factory('Status')
				->sqlField("CASE WHEN NOW() > enddate THEN 'I' ELSE 'A' END")
				->value('A')
		);

		$list->addColumn('Category');
		$list->addColumn('Active')->type('switch')->sqlField('status');

		$list->addURL = 'form_category.php';
		$list->editURL = 'form_category.php';
		
		
		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_fif_form_category')
				->setKeyField('fcrefid')
				->applyListClassMode()
		);

		$list->printList();
	}
?>