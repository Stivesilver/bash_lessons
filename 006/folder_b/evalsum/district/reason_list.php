<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Reason for Evaluation';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT rrefid,
			   rseq,
			   rdesc,
			   CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END  as status
		  FROM webset.es_disdef_eval_reason
		 WHERE (1=1) ADD_SEARCH
		   AND vndrefid = VNDREFID
		 ORDER BY rseq, rdesc
    ";

	$list->addRecordsResequence(
		'webset.es_disdef_eval_reason',
		'rseq'
	);

	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Reason')->sqlField('rdesc');
	$list->addColumn('Status')->sqlField('status')->type('switch');

	$list->addURL = './referral_edit.php';
	$list->editURL = './referral_edit.php';

	$list->addButton(FFIDEAExportButton::factory()
		->setTable('webset.es_disdef_eval_reason')
		->setKeyField('rrefid')
		->applyListClassMode());

	$list->printList();
?>
