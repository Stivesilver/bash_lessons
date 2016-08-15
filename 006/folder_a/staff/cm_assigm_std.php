<?php
    Security::init();
    $umrefid = io::get('umrefid');

    $list = new ListClass();

	$list->showSearchFields = true;

    $list->SQL = "
                SELECT tsrefid,
                       " . IDEAParts::get('stdname') . ",
                       " . IDEAParts::get('schoolName') . ",
                       gl_code,
                       stdenterdt,
                       CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
                       CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus,
                       stdcmpltdt < NOW() as stdcmpltdt_chk,
                       stdtriennialdt < NOW() as stdtriennialdt_chk
                  FROM webset.sys_teacherstudentassignment ts
                       " . IDEAParts::get('studentJoin') . "
                       " . IDEAParts::get('gradeJoin') . "
                       " . IDEAParts::get('schoolJoin') . "
                 WHERE std.vndrefid = VNDREFID
                   AND umrefid = " . $umrefid . "
                       ADD_SEARCH
                 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
            ";

	$list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->value('A');
    $list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
        ->value('A')
        ->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END");

    $list->addColumn('Student Name');
    $list->addColumn('Attending School');
    $list->addColumn('Grade')->sqlField('gl_code');
    $list->addColumn('Enrollmnent Date')->hint('Grade Level')->sqlField('stdenterdt')->type('date');
    $list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus');
    $list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

    $list->addRecordsProcess('Delete')
        ->url(CoreUtils::getURL('cm_assigm_delete.ajax.php'))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);

    $list->addButton('Add Student(s)')
        ->onClick('selectStudent()');

    $list->printList();

    print FormField::factory('hidden')->name('umrefid')->value($umrefid)->toHTML();
?>
<script type="text/javascript">
    function selectStudent() {
        var wnd = api.window.open('Add Student(s)', api.url('cm_assigm_sel.php', {'umrefid': $('#umrefid').val()}));
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('student_assigned', onEvent);
        wnd.show();
    }

    function onEvent(e) {
        api.reload();
    }

</script>

