<?php

	Security::init();

	$list = new ListClass();

	$list->title            = 'Building Marking Periods';
	$list->showSearchFields = true;
	$list->SQL              = "
        SELECT smp.smp_refid,
               smp.smp_period,
               smp.esy,
               smp_active
          FROM webset.sch_marking_period smp
         WHERE vndrefid = VNDREFID ADD_SEARCH
         ORDER BY smp_sequens
    ";

	$list->addSearchField('Status', 'smp_active', 'list')
		->name('spr_active')
		->data(
			array('Y' => 'Yes', 'N' => 'No')
		)
		->value('Y');

	$list->addRecordsResequence('webset.sch_marking_period', 'smp_sequens');

	$list->addColumn('Period');
	$list->addColumn('ESY')->type('switch');
	$list->addColumn('Active')->type('switch');

	$list->addURL  = 'prog_report_edit.php';
	$list->editURL = 'prog_report_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sch_marking_period')
			->setKeyField('smp_refid')
			->applyListClassMode()
	);

	$list->printList();
?>
