<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Exception Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
        SELECT tsrefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
               stddob,
               " . IDEAParts::get('stdage') . " AS stdage,
               gl_code,
               srusererrdesc,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment AS ts
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               LEFT OUTER JOIN public.sys_usermst AS u ON u.umrefid = ts.umrefid
               INNER JOIN webset.std_err err ON ts.tsrefid = err.stdrefid
               INNER JOIN webset.err_systemreference SR ON SR.srrefid = err.esrefid
               INNER JOIN webset.err_categorydef CD ON SR.ecrefid=CD.ecrefid
               INNER JOIN webset.err_leveldef LD ON SR.ldrefid=LD.ldrefid
         WHERE std.vndrefid = VNDREFID
         ORDER BY 1,2,srusererrdesc
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField('Age Range', IDEAParts::get('stdage'), 'intrange')
		->name('stdage');

	$list->addSearchField('Exception', '', 'list')
		->name('exception')
		->sqlField('err.esrefid')
		->sql("
            SELECT srrefid, srusererrdesc
              FROM webset.err_systemreference err
             WHERE EXISTS (SELECT 1
                             FROM webset.dmg_studentmst AS s
                                  INNER JOIN webset.sys_teacherstudentassignment AS t ON t.stdrefid = s.stdrefid
                                  INNER JOIN webset.std_err STD ON t.tsrefid = STD.stdrefid
                            WHERE err.srrefid = STD.esrefid
                              AND s.vndrefid = VNDREFID
                              AND COALESCE(srstate,'" . $state . "') = '" . $state . "')
             ORDER BY 2
        "
	);

	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addColumn('School Name', '', 'group')->sqlField('vouname');
	$list->addColumn('Student Name', '10%')->sqlField('stdname');
	$list->addColumn('Grade', '7%')->sqlField('gl_code');
	$list->addColumn('DOB', '7%')->sqlField('stddob')->type('date');
	$list->addColumn('Age', '6%')->sqlField('stdage');

	if ($state == 'TX') {
		$list->addColumn('Long ARD', '10%')->dataCallback('getArdDate');
	}

	$list->addColumn('Exception', '55%')->sqlField('srusererrdesc');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

	$list->printList();

	function getArdDate($data, $col) {
		return db::execSQL("
            SELECT to_char(longard, 'mm/dd/yy')
              FROM webset_tx.std_dates dt
                   INNER JOIN webset.std_iep_year iep ON dt.iepyear = iep.siymrefid
             WHERE iep.stdrefid = " . $data['tsrefid'] . "
               AND siymcurrentiepyearsw = 'Y'
        ")->getOne();
	}

?>
