<?php

	Security::init();

	$elrefid = io::geti('elrefid');

	$list = new ListClass();
	$list->title = 'Eligibility Sub Criteria';
	$list->showSearchFields = true;

	$list->SQL = "
		SELECT elsrefid,
		       elsdesc,
		       seq_num,
		       CASE
		       WHEN NOW() > recdeactivationdt THEN 'N'
		       ELSE 'Y'
		       END AS status
		  FROM webset.es_statedef_eligibility_sub AS t
		 WHERE elrefid = $elrefid
		 ORDER BY seq_num, elsdesc
	";

	$list->addSearchField("ID", "(elsrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sqlField('(CASE recdeactivationdt<now() WHEN true THEN 2 ELSE 1 END)')
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'In-Active')")
		->value(1);

	$list->addColumn('ID')->sqlField('elsrefid');
	$list->addColumn("Eligibility Criteria Sub")->sqlField('elsdesc');
	$list->addColumn("Status")->sqlField('status')->type('switch');
	$list->addColumn("Order #")->sqlField('seqnum');

	$list->addURL = CoreUtils::getURL('./eligibility_sub_edit.php', array('elrefid' => io::get("elrefid")));
	$list->editURL = CoreUtils::getURL('./eligibility_sub_edit.php', array('elrefid' => io::get("elrefid")));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_statedef_eligibility_sub')
			->setKeyField('elsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
