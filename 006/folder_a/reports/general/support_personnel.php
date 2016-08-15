<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Support for School Personnel';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE);

	$list->SQL = "
		 SELECT sspmrefid,
                sspdesc,
                sspbegdate,
                sspenddate,
                sspnarrative,
                nasw,
                " . IDEAParts::get('stdname') . " AS stdname,
		        CASE
		        WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		        ELSE 'N'
		        END AS stdstatus,
		        CASE
		        WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		        ELSE 'N'
		        END AS spedstatus
           FROM webset.sys_teacherstudentassignment AS ts
                INNER JOIN webset.std_srv_supppersonnel AS supp ON (ts.tsrefid = supp.stdrefid)
                " . IDEAParts::get('studentJoin') . "
                INNER JOIN webset.statedef_services_supppersonnel state ON (supp.ssprefid = state.ssprefid)
          WHERE std.vndrefid = VNDREFID
          ORDER BY seqnum, sspdesc
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(
		FFSelect::factory('Service')
			->sql("
				 SELECT sspdesc, sspdesc
	               FROM webset.statedef_services_supppersonnel
	              WHERE screfid = " . VNDState::factory()->id . "
	                AND (enddate IS NULL or now()< enddate)
	              ORDER BY seqnum, sspdesc
			")
			->sqlField('sspdesc'));
	$list->addSearchField('Narrative')->sqlField('sspnarrative')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');

	$list->addColumn('Service')->sqlField('sspdesc');
	$list->addColumn('Begin Date')->sqlField('sspbegdate');
	$list->addColumn('End Date')->sqlField('sspenddate');
	$list->addColumn('Narrative')->sqlField('sspnarrative');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();

	function parttwo($data, $col) {
		global $list;
		$accs = db::execSQL("
			SELECT accdesc
			  FROM webset.statedef_aa_acc AS sta
				   INNER JOIN webset.std_form_d_acc AS std ON (std.accrefid = sta.accrefid AND std.stdrefid = " . $data['tsrefid'] . "  AND std.syrefid = " . $data['syrefid'] . ")
			 WHERE (enddate IS NULL OR NOW ()< enddate)
			 ORDER BY seq_num
		")->assocAll();
		$res = '';
		$i = 0;
		if ($list->isPrintMode()) {
			foreach ($accs as $acc) {
				if ($i == 0) {
					$res .= $acc['accdesc'];
				} else {
					$res .= "\n" . $acc['accdesc'];
				}
				$i++;
			}
		} else {
			foreach ($accs as $acc) {
				if ($i == 0) {
					$res .= $acc['accdesc'];
				} else {
					$res .= '</br>' . $acc['accdesc'];
				}
				$i++;
			}
		}
		return $res;
	}

?>
