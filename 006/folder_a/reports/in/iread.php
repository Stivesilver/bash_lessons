<?php
	Security::init();

	$list = new listClass();
	$list->title = 'IRead';
	$list->showSearchFields = true;
	$list->printable = true;
	$area_id = 157;

	$quests = db::execSQL("
        SELECT refid,
               validvalue
          FROM webset.disdef_validvalues
         WHERE vndrefid = VNDREFID
           AND valuename = 'IN_IRead'
           AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = '1'
         ORDER BY valuename, sequence_number, validvalue ASC
	")->assocAll();

	$sql_columns = array();
	foreach ($quests as $question) {
		$name = 'question_' . $question['refid'];
		$sql_columns[] = "(xpath('/record/question_" . $question['refid'] . "/text()', txt02::xml))[1]";
	}

	$list->SQL = "
        SELECT std.stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           dat01,
	           txt01,
	           ditrdesc,
	           dat01,
	           " . implode(',', $sql_columns) . "
	      FROM webset.sys_teacherstudentassignment ts
          	   " . IDEAParts::get('studentJoin') . "	           
	           " . IDEAParts::get('repSchoolJoin') . "
	           " . IDEAParts::get('gradeJoin') . "
	           INNER JOIN webset.std_general test ON ts.tsrefid = test.stdrefid AND area_id = $area_id
	           LEFT OUTER JOIN webset.disdef_in_test_rating rat ON test.int01 = ditrrefid
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
	$list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
	$list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
		->value('A')
		->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
		->name('spedstatus');

	$list->addSearchField('Subject', 'ohsdtrefid', 'list')
		->sql("
            SELECT ohsdtrefid, ohsdtdesc
	          FROM webset.statedef_oh_sw_dw_test
	         WHERE (enddate IS NULL or now()< enddate)
	           AND screfid = " . VNDState::factory()->id . "
             ORDER BY ohsdtdisplayseq
        ");

	$list->addSearchField(FFGradeLevel::factory())
		->sqlField('std.gl_refid')
		->caption('Actual Grade');

	foreach ($quests as $question) {
		$name = 'question_' . $question['refid'];
		$list->addSearchField(
			FFSwitchYN::factory($question['validvalue'])
		)->sqlField("(xpath('/record/question_" . $question['refid'] . "/text()', txt02::xml))[1]");

	}


	$list->addColumn('Student Name');
	$list->addColumn('Reporting School');
	$list->addColumn('Actual Grade');
	$list->addColumn('Date')->type('date');
	$list->addColumn('Score');
	$list->addColumn('Rating');
	$list->addColumn('Conference Date')->type('date');
	foreach ($quests as $question) {
		$list->addColumn($question['validvalue'])->type('switch');
	}


	$list->printList();

	function showGrades($data, $col) {
		global $grades;
		$arrayCode = array();

		foreach (explode(',', $data[$col]) as $grade_id) {
			if ($grade_id > 0) $arrayCode[] = $grades[$grade_id];
		}

		return implode(', ', $arrayCode);
	}

?>