<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$path = '/apps/idea/iep.id/2013/postgoals/by_year_list.php';

	$area_id = IDEAAppArea::ID_SEC_ASSESSMENT_SUMMARY;

	if ($RefID > 0 || $RefID == '0') {

		$edit = new EditClass("edit1", $RefID);

		$edit->setSourceTable('webset.std_general', 'refid');

		$edit->title = 'Transition Assessment';

		$edit->addGroup('General Information');
		$edit->addControl('Order #', 'integer')
			->sqlField('order_num')
			->value(
				(int) db::execSQL("
					SELECT max(order_num)
					  FROM webset.std_general
					 WHERE iepyear = " . $stdIEPYear . "
					   AND area_id = " . $area_id . "
	            ")->getOne() + 1
			)
			->size(5);
		$edit->addControl('Transition Assessment Tool', 'select')
			->sqlField('int01')
			->name('int01')
			->sql("
				SELECT refid,
					   validvalue
				  FROM webset.disdef_validvalues
				 WHERE vndrefid = VNDREFID
				   AND valuename = 'ID_PostGoals_Assessment'
				   AND (glb_enddate IS NULL or now()< glb_enddate)
				 ORDER BY sequence_number, validvalue ASC
			");

    	 $edit->addControl('Specify')
			->sqlField('txt01')
			->name('txt01')
			->showIf('int01', db::execSQL("
                                  SELECT refid
                                    FROM webset.disdef_validvalues
                                   WHERE substring(lower(validvalue), 1, 5) = 'other'
								     AND vndrefid = VNDREFID
									 AND valuename = 'ID_PostGoals_Assessment'
                                 ")->indexAll())
			->size(50);

		$edit->addControl('Date', 'date')->sqlField('dat01');

		$edit->addControl('Summary of Results', 'textarea')
			->sqlField('txt02')
			->width('100%')
			->css('height', '150px')
			->autoHeight(true);


		$edit->addGroup('Update Information');
		$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
		$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

		$edit->firstCellWidth = '35%';
		$edit->finishURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));

		$edit->printEdit();
	} else {

		$list = new ListClass();

		$list->title = '1. Assessment Summary for Transition Services Planning (maintain cumulative record of assessments):';

		$list->SQL = "
			SELECT std.refid,
				   order_num,
				   COALESCE(txt01, validvalue),
				   dat01,
				   txt02
			  FROM webset.std_general std
			  	   LEFT OUTER JOIN webset.disdef_validvalues subj ON subj.refid = std.int01
			 WHERE iepyear = " . $stdIEPYear . "
			   AND area_id = " . $area_id . "
			 ORDER BY order_num, std.refid
		";


		$list->addColumn('Order #');
		$list->addColumn('Transition Assessment Tool');
		$list->addColumn('Date', '', 'date');
		$list->addColumn('Summary of Results');

		$list->addURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));

		$list->deleteKeyField = 'refid';
		$list->deleteTableName = 'webset.std_general';

		$list->addRecordsResequence(
			'webset.std_general',
			'order_num'
		);

		$button = new IDEAPopulateIEPYear($dskey, $area_id, $path);
		$listButton = $button->getPopulateButton();
		$list->addButton($listButton);

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($list->deleteTableName)
				->setKeyField($list->deleteKeyField)
				->applyListClassMode()
		);

		$list->addButton(
			IDEAFormat::getPrintButton(array('dskey' => $dskey))
		);

		$list->printList();
	}
?>
