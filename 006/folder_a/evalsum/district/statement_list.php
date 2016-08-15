<?php

	Security::init();

	if (io::get('area') == 'R' && io::get('section') != -1) {
		$section_where = 'AND acategory::int = ' . io::get('section');
	} else {
		$section_where = '';
	}

	$list = new ListClass();

	$list->title = 'Form Statements';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT ssgirefid,
			   CASE area
			   WHEN 'E' THEN 'Evaluation Report'
			   WHEN 'R' THEN 'Review of Existing Data'
			   END as document,
			   CASE acategory
			   WHEN '1' THEN 'Description of Data Reviewed'
			   WHEN '2' THEN 'Summary of Information Gained'
			   END as section,
			   screen.scrdesc,
			   ssgitext,
			   sel.lastuser,
			   sel.lastupdate
		  FROM webset.es_formdisselections as sel
			   INNER JOIN webset.es_statedef_screeningtype as screen ON sel.screening_id = screen.scrrefid
		 WHERE (1=1) ADD_SEARCH
		   AND vndrefid = VNDREFID
		   $section_where
		 ORDER BY document, screen.scrseq, sel.seq_ord, sel.ssgitext
    ";

	$list->addRecordsResequence(
		'webset.es_formdisselections',
		'seq_ord'
	);

	$list->addSearchField('Document', 'area', 'select')
		->data(
			array(
				'E' => 'Evaluation Report',
				'R' => 'Review of Existing Data'
			)
		)
		->sqlField('area')
		->name('area');

	$list->addSearchField('Section', '', 'select')
		->name('section')
		->data(
			array(
				'1' => 'Description of Data Reviewed',
				'2' => 'Summary of Information Gained'
			)
		)
		->showIf('area', 'R');

	$list->addSearchField(FFIDEAEvalScreenType::factory())
		->sqlField('screen.scrrefid');

	$list->addSearchField('Text in Statement', "ssgitext ILIKE '%' || ADD_VALUE || '%'");

	$list->addColumn('Document')->sqlField('document')->type('group');
	$list->addColumn('Area')->sqlField('scrdesc');
	$list->addColumn('Section')->sqlField('section');
	$list->addColumn('Statement')
		->sqlField('ssgitext')
		->css('overflow', 'hidden')
		->css('text-overflow', 'ellipsis')
		->css('max-width', '300px')
		->css('white-space', 'nowrap');

	$list->addURL = 'statement_edit.php';
	$list->editURL = 'statement_edit.php';

	$list->deleteKeyField = 'ssgirefid';
	$list->deleteTableName = 'webset.es_formdisselections';

	$list->addButton(FFIDEAExportButton::factory()
		->setTable('webset.es_formdisselections')
		->setKeyField('ssgirefid')
		->applyListClassMode());

	$list->printList();
?>
