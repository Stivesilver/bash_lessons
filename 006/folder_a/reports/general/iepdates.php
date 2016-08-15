<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Resident District Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$monthSQL = "SELECT NULL, 'ALL', 1";
	for ($i = -12; $i < 13; $i++) {
		$monthSQL .= " UNION SELECT $i, TO_CHAR(now()+interval '$i month', 'Month YY'), 2";
	}
	$monthSQL .= " ORDER BY 3, 1";

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code as grade,
	           " . IDEAParts::get('stdiepmeetingdt') . " AS stdiepmeetingdt,
	           " . IDEAParts::get('stdcmpltdt') . " AS stdcmpltdt,
	           " . IDEAParts::get('stdevaldt') . " AS stdevaldt,
	           " . IDEAParts::get('stdtriennialdt') . " AS stdtriennialdt,
	           to_char(stddraftiepcopydt, 'mm-dd-yyyy') as stddraftiepcopydt,
	           to_char(stdiepcopydt, 'mm-dd-yyyy')      as stdiepcopydt,
	           to_char(previousiepdt, 'mm-dd-yyyy')     as previousiepdt,
	           to_char(parentrightdt, 'mm-dd-yyyy')     as parentrightdt,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
	     WHERE std.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY grade, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEADisability::factory());
	$list->addSearchField('Gifted Program', 'giftedprogram', 'list')->sql($monthSQL);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Grade', '', 'group')->sqlField('grade');

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('IEP Review Date')->sqlField('stdcmpltdt');
	$list->addColumn('Evaluation Date')->sqlField('stdevaldt');
	$list->addColumn('Triennal Eval Date')->sqlField('stdtriennialdt');
	if (VNDState::factory()->id == 25) {
		$list->addColumn('Draft IEP copy')->sqlField('stddraftiepcopydt');
		$list->addColumn('IEP copy')->sqlField('stdiepcopydt');
		$list->addColumn('Previous Annual IEP')->sqlField('previousiepdt');
		$list->addColumn('Copy of Bill of Rights')->sqlField('parentrightdt');
	}
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
