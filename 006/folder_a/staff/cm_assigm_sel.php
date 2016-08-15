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
                   AND umrefid IS NULL
                       ADD_SEARCH
                 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
            ";

    $list->addSearchField('Last Name', 'stdlnm');
    $list->addSearchField('First Name', 'stdfnm');
    $list->addSearchField(FFIDEASchool::factory());
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->value('A')->name('stdstatus');
    $list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
        ->value('A')
        ->name('spedstatus')
        ->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END");
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid')->name('gl_refid');

    $list->addColumn('Student Name');
    $list->addColumn('Attending School');
    $list->addColumn('Grade')->sqlField('gl_code');
    $list->addColumn('Enrollmnent Date')->hint('Grade Level')->sqlField('stdenterdt')->type('date');
    $list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus');
    $list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus');

    $list->addRecordsProcess('Assign')
        ->url(CoreUtils::getURL('cm_assigm_process.ajax.php', array('umrefid' => $umrefid)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->onProcessDone('assignDone')
        ->progressBar(false);

    $list->printList();
?>
<script type="text/javascript">
    function assignDone() {
        api.window.dispatchEvent('student_assigned');
        api.window.destroy();
    }
</script>