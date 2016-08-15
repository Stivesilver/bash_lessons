<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student_name = ucfirst(strtolower(IDEAStudent::factory($tsRefID)->get('stdfirstname')));
	$path = '/apps/idea/iep.id/2013/postgoals/by_year_postgoals_list.php';


	$area_id = IDEAAppArea::ID_SEC_POST_GOALS;

	if ($RefID > 0 || $RefID == '0') {

		$edit = new EditClass("edit1", $RefID);

		$edit->setSourceTable('webset.std_general', 'refid');

		$edit->title = 'Postsecondary Goals';

		$edit->addGroup('General Information');

		$edit->addControl('Order #', 'integer')
			->sqlField('order_num')
			->value(
				(int)db::execSQL("
					SELECT max(order_num)
					  FROM webset.std_general
					 WHERE iepyear = " . $stdIEPYear . "
					   AND area_id = " . $area_id . "
	            ")->getOne() + 1
			)
			->size(5);

		$edit->addControl('Area', 'select')
			->sqlField('int01')
			->sql(IDEADef::getValidValueSql('ID_Secondary_Activity', "refid, validvalue"));

		$edit->addControl(
			FFIDEASchoolYear::factory()
				->sqlField('int03')
		);

		$edit->addControl('Statement Option', 'select')
			->sqlField('int02')
			->sql(IDEADef::getValidValueSql('ID_Secondary_Post_Goals_Statements', "refid,  REPLACE(validvalue, 'student', '" . db::escape($student_name) . "')"));

		$edit->addControl('Goal', 'textarea')
			->sqlField('txt01')
			->width('100%')
			->css('height', '150px')
			->autoHeight(true);

		$edit->addGroup('Update Information');
		$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
		$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

		$edit->firstCellWidth = '35%';
		$edit->finishURL = CoreUtils::getURL('postgoals.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('postgoals.php', array('dskey' => $dskey));

		$edit->printEdit();
	} else {

		$list = new ListClass();

		$list->title = 'Postsecondary Goals';

		$list->SQL = "
			SELECT std.refid,
				   order_num,
				   area.validvalue,
				   dsydesc,
				   REPLACE(stm.validvalue, 'student', '" . db::escape($student_name) . "'),
				   txt01
			  FROM webset.std_general std
			  	   LEFT OUTER JOIN webset.glb_validvalues area ON area.refid = std.int01
			  	   LEFT OUTER JOIN webset.glb_validvalues stm ON stm.refid = std.int02
			  	   LEFT OUTER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = std.int03
			 WHERE iepyear = " . $stdIEPYear . "
			   AND area_id = " . $area_id . "
			 ORDER BY order_num, std.refid
		";

		$list->addColumn('Order #');
		$list->addColumn('Area');
		$list->addColumn('School Year');
		$list->addColumn('Goal')->dataCallback('composeGoal');

		$list->addURL = CoreUtils::getURL('postgoals.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('postgoals.php', array('dskey' => $dskey));

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

	function composeGoal($data, $col) {
		return $data[$col] . ' ' . $data['txt01'];
	}

?>
