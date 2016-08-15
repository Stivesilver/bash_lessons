<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Screening Types';
	$list->showSearchFields = true;

	$list->SQL = "
		SELECT scrrefid,
			   scrseq,
			   scrdesc,
			   CASE
		       WHEN NOW() > enddate THEN 'N'
		       ELSE 'Y'
		       END AS status
          FROM webset.es_statedef_screeningtype AS scr
               INNER JOIN webset.glb_statemst AS stm ON stm.staterefid = scr.screfid
         WHERE scr.screfid = $staterefid
         ORDER BY state, scrseq, scrdesc
	";


	$list->addSearchField("ID", "(scrrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'In-Active')")
		->value(1);

	$list->addColumn("ID")->sqlField('scrrefid');
	$list->addColumn("Seq #")->sqlField('scrseq');
	$list->addColumn("Screening Types")->sqlField('scrdesc');
	$list->addColumn("Status")->sqlField('status')->type('switch');

	$list->addURL = CoreUtils::getURL('./scr_type_edit.php', array('staterefid' => io::get("staterefid")));
	$list->editURL = CoreUtils::getURL('./scr_type_edit.php', array('staterefid' => io::get("staterefid")));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_statedef_screeningtype')
			->setKeyField('scrrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
