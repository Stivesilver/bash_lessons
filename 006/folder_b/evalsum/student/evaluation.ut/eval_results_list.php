<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$staterefid = VNDState::factory()->id;

	$list = new ListClass();

	$list->title = 'Evaluation Results';

	$list->SQL = "
		SELECT std.esrefid,
		       scr.scrdesc,
			   std.screen_summary,
			   std.further_assess_needed_sw
		  FROM webset.es_std_join AS std
		       INNER JOIN webset.es_statedef_screeningtype AS scr ON scr.scrrefid = std.screening_id
		 WHERE eprefid = " . $evalproc_id . "
		 ORDER BY scr.scrseq
    ";

	$list->addColumn('Area')
		->sqlField('scrdesc');
	$list->addColumn('Summary Results')
		->css('overflow', 'hidden')
		->css('text-overflow', 'ellipsis')
		->css('max-width', '300px')
		->css('white-space', 'nowrap')
		->sqlField('screen_summary');
	$list->addColumn('Further assessment needed')
		->sqlField('further_assess_needed_sw')
		->type('switch');

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled(db::execSQL("
                SELECT 1
                  FROM webset.es_statedef_screeningtype st
                 WHERE st.screfid = " . $staterefid . "
                   AND (st.enddate>now() OR st.enddate IS NULL OR scrrefid IN ( " . (IDEACore::disParam(155) ? IDEACore::disParam(155) : '0') . "))
                   AND st.scrrefid NOT IN (
						SELECT COALESCE(screening_id, 0)
						  FROM webset.es_std_join
						 WHERE stdrefid = " . $tsRefID . "
						   AND eprefid = " . $evalproc_id . "
                       )
            ")->getOne() != '1');


	$list->addURL = CoreUtils::getURL('./eval_results_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./eval_results_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.es_std_join';
	$list->deleteKeyField = 'esrefid';

	$list->addButton(FFIDEAExportButton::factory()
		->setTable($list->deleteTableName)
		->setKeyField($list->deleteKeyField)
		->applyListClassMode());

	$list->addButton(IDEAFormat::getPrintButton(array('dskey' => $dskey)));

	$list->printList();
