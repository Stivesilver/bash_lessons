<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = "yes";

	$list->SQL = "
		SELECT drefid,
		       doctype,
		       docdesc,
		       block_class,
		       preview_gen_file,
		       defaultdoc,
		       seqnum,
		       CASE
		       WHEN NOW() > enddate THEN 'N'
		       ELSE 'Y'
		       END AS status
		  FROM webset.sped_doctype
		 WHERE setrefid = " . io::get("iepformat") . "
		 ORDER BY seqnum, doctype
    ";

	$list->addSearchField("ID", "(drefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'In-Active')");

	$list->title = "Doc Types";

	$list->addColumn('ID')->sqlField('drefid');
	$list->addColumn("Doc Type")->sqlField('doctype');
	$list->addColumn("Doc Desc")->sqlField('docdesc');
	$list->addColumn("Block Class")->sqlField('block_class');
	$list->addColumn("Preview Gen File")->sqlField('preview_gen_file');
	$list->addColumn("Default Doc")->sqlField('defaultdoc')->type('switch');
	$list->addColumn("Status")->sqlField('status')->type('switch');
	$list->addColumn("Order #")->sqlField('seqnum');

	$list->addRecordsResequence(
		'webset.sped_doctype',
		'seqnum'
	);

	$list->addURL = CoreUtils::getURL('./iep_docs_edit.php', array('iepformat' => io::get("iepformat")));
	$list->editURL = CoreUtils::getURL('./iep_docs_edit.php', array('iepformat' => io::get("iepformat")));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_doctype')
			->setKeyField('drefid')
			->setNesting('webset.sped_iepblocks', 'ieprefid', 'ieptype', 'webset.sped_doctype', 'drefid')
			->applyListClassMode()
	);

	$list->printList();
?>
