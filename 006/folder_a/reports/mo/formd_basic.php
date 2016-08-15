<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form D: Assessments and Accommodations';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus,
			   TRIM(
				   CASE WHEN fd.young_map = 'Y' THEN 'MAP 3-8, ' ELSE '' END ||
				   CASE WHEN fd.assessments = 'Y' THEN 'MAP 9-12, ' ELSE '' END  ||
				   CASE WHEN fd.addeoc_m = 'Y' THEN 'EOC' ||
						CASE WHEN fd.addeoc_ass != '' OR fd.addeoc_ws != '' OR fd.addeoc_os != '' THEN '(' ||
							CASE WHEN fd.addeoc_ass != '' THEN fd.addeoc_ass || '; ' ELSE '' END ||
							CASE WHEN fd.addeoc_os != '' THEN
								 'Without: ' || array_to_string(ARRAY(SELECT progdesc
													   FROM webset.statedef_aa_prog
													  WHERE ',' || fd.addeoc_os || ',' LIKE '%,' || progrefid::VARCHAR || ',%'), ', ') || '; '
							ELSE '' END ||
							CASE WHEN fd.addeoc_ws != '' THEN
								 'With: ' || array_to_string(ARRAY(SELECT progdesc
													   FROM webset.statedef_aa_prog
													  WHERE ',' || fd.addeoc_ws || ',' LIKE '%,' || progrefid::VARCHAR || ',%'), ', ')
							ELSE '' END
						|| '), ' ELSE '' END
				   ELSE '' END ||
				   CASE WHEN fd.exempt = 'Y' THEN 'EXEMPT' ||
						CASE WHEN fd.exempt_ass != '' OR fd.exempts != '' THEN '(' ||
							CASE WHEN fd.exempt_ass != '' THEN fd.exempt_ass || '; ' ELSE '' END ||
							CASE WHEN fd.exempts != '' THEN
								 'Assessment(s): ' || array_to_string(ARRAY(SELECT progdesc
													   FROM webset.statedef_aa_prog
													  WHERE ',' || fd.exempts || ',' LIKE '%,' || progrefid::VARCHAR || ',%'), ', ')
							ELSE '' END
						|| '), ' ELSE '' END
				   ELSE '' END ||
				   CASE WHEN fd.act = 'Y' THEN 'ACT, ' ELSE ', ' END ||
				   CASE WHEN fd.young_map_a = 'Y' THEN 'MAP-A 3-8, ' ELSE ', ' END ||
				   CASE WHEN fd.eligible = 'Y' THEN 'MAP-A 9-12, ' ELSE ', ' END ||
				   CASE WHEN fd.act_mapa = 'Y' THEN 'MAP-A ACT, ' ELSE ', ' END, 
				   ', '
				) AS part1,
		       ts.tsrefid,
		       fd.syrefid
		  FROM webset.std_form_d AS fd 
		       INNER JOIN webset.std_iep_year iep ON fd.syrefid = iep.siymrefid AND siymcurrentiepyearsw = 'Y'
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON (fd.stdrefid = ts.tsrefid)
		       " . IDEAParts::get('studentJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm 
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(
		'Assessment', 
		"
			CASE 
				WHEN 'ADD_VALUE' = 'fd.young_map' THEN fd.young_map = 'Y'
				WHEN 'ADD_VALUE' = 'fd.young_o' THEN fd.young_o = 'Y'
				WHEN 'ADD_VALUE' = 'fd.young_w' THEN fd.young_w = 'Y'
				WHEN 'ADD_VALUE' = 'fd.young_map_a' THEN fd.young_map_a = 'Y'
				WHEN 'ADD_VALUE' = 'fd.assessments' THEN fd.assessments = 'Y'
				WHEN 'ADD_VALUE' = 'fd.accommparticip' THEN fd.accommparticip = 'Y'
				WHEN 'ADD_VALUE' = 'fd.accommmath' THEN fd.accommmath = 'Y'
				WHEN 'ADD_VALUE' = 'fd.eligible' THEN fd.eligible = 'Y'
				WHEN 'ADD_VALUE' = 'fd.addeoc_m' THEN fd.addeoc_m = 'Y'
				WHEN 'ADD_VALUE' = 'fd.addeoc_o' THEN fd.addeoc_o = 'Y'
				WHEN 'ADD_VALUE' = 'fd.addeoc_w' THEN fd.addeoc_w = 'Y'
				WHEN 'ADD_VALUE' = 'fd.exempt' THEN fd.exempt = 'Y'
				WHEN 'ADD_VALUE' = 'fd.act' THEN fd.act = 'Y'
				WHEN 'ADD_VALUE' = 'fd.act_o' THEN fd.act_o = 'Y'
				WHEN 'ADD_VALUE' = 'fd.act_w' THEN fd.act_w = 'Y'
				WHEN 'ADD_VALUE' = 'fd.act_mapa' THEN fd.act_mapa = 'Y'
			END
		", 
			'select'
)->data(
			array(
				'fd.young_map' => 'MAP 3-8',
				'fd.young_o' => 'MAP 3-8 without accommodations',
				'fd.young_w' => 'MAP 3-8 with accommodations',
				'fd.young_map_a' => 'MAP-A 3-8',

				'fd.assessments' => 'MAP 9-12',
				'fd.accommparticip' => 'MAP 9-12 without accommodations',
				'fd.accommmath' => 'MAP 9-12 with accommodations',
				'fd.eligible' => 'MAP-A 9-12',

				'fd.addeoc_m' => 'EOC',
				'fd.addeoc_o' => 'EOC without accommodations',
				'fd.addeoc_w' => 'EOC with accommodations',
				'fd.exempt' => 'EXEMPT',

				'fd.act' => 'ACT',
				'fd.act_o' => 'ACT without accommodations',
				'fd.act_w' => 'ACT with accommodations',
				'fd.act_mapa' => 'ACT MAP-A'
			)
		);

	$list->addSearchField('Accommodation', '', 'select')
		->sqlField("
			EXISTS (
					SELECT 1
					  FROM webset.std_form_d_acc AS acc
					 WHERE acc.syrefid = fd.syrefid
					   AND acc.accrefid = ADD_VALUE
				   )
			")
		->sql("
			SELECT accrefid,
			       accdesc
			  FROM webset.statedef_aa_acc AS sta
				   inner join webset.statedef_aa_cat As cat on cat.catrefid = sta.acccat
			 WHERE (sta.enddate IS NULL OR NOW ()< sta.enddate)
			   and (cat.enddate IS NULL OR NOW ()< cat.enddate)
			   and cat.screfid = " . VNDState::factory()->id . "
			 ORDER BY catrefid, seq_num
        ");

	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('State Assessments')->sqlField('part1')->dataCallback('line2rows');
	$list->addColumn('Accommodations')->dataCallback('parttwo');
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
					$res .= '<br/>' . $acc['accdesc'];
				}
				$i++;
			}
		}
		return $res;
	}

	function line2rows($data, $col) {
		$res = implode("\n<br/>", explode(', ', $data['part1']));
		return $res;
	}
?>
