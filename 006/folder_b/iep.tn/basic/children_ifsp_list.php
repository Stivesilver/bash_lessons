<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
				SELECT ts.tsrefid,
				       ts.stdrefid,
				       stdlnm,
				       stdfnm,
				       stdmnm,
				       " . IDEAParts::get('schoolName') . " || ' ' || COALESCE(' - ' || " . IDEAParts::get('username') . ", '') AS school,
				       gl_code,
				       " . IDEAParts::get('stdiepmeetingdt') . " AS stdiepmeetingdt,
				       CASE
				       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
				       ELSE 'N'
				       END AS stdstatus,
				       CASE
				       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
				       ELSE 'N'
				       END AS spedstatus,
				       stdcmpltdt AS stdcmpltdt_real,
				       stdtriennialdt AS stdtriennialdt_real,
				       stdlnm || ', ' || stdfnm
				  FROM webset.sys_teacherstudentassignment ts " . IDEAParts::get('studentJoin') . " " . IDEAParts::get('gradeJoin') . " " . IDEAParts::get('casemanJoin') . " " . IDEAParts::get('schoolJoin') . " " . IDEAParts::get('enrollJoin') . "
				 WHERE std.vndrefid = VNDREFID ADD_SEARCH
				 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
    ";

	$list->addSearchField('Last Name', 'stdlnm');
	$list->addSearchField('First Name', 'stdfnm');
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField('Student #', 'stdschid');
	$list->addSearchField('Federal #', 'stdfedidnmbr');
	$list->addSearchField('State #', 'stdstateidnmbr');
	$list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
	$list->addSearchField(FFIDEASpEdStatus::factory())->caption('IFSP Status');
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid')->name('gl_refid');

	$list->addColumn('Lumen ID');
	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('Middle Name');
	$list->addColumn('Attending School');
	$list->addColumn('Grade');
	$list->addColumn('Meeting Date');
	$list->addColumn('Status')->hint('Student Status')->type('switch')->sqlField('stdstatus');
	$list->addColumn('IFSP Status')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus');

	$list->editURL = 'javascript:openWindow(AF_REFID)';
	$list->prepareRow('prepareLine');

	function prepareLine(ListClassRow $row) {
		$row->onClick('openStdScreen(' . json_encode(CryptClass::factory()->encode($row->dataID)) . ')');
	}

	$list->printList();

?>
<script type='text/javascript'>
	function openStdScreen(refid) {
		var win = api.desktop.open('Loading...', api.url('<?= CoreUtils::getURL('/apps/idea/iep/desktop/desk_main.php'); ?>', {'tsRefID': refid}));
		win.maximize();
		win.show();
	}
</script>
