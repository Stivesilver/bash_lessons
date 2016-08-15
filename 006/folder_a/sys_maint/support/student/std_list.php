<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Student Demographics';
    $list->showSearchFields = true;

    $list->SQL="
        SELECT tsrefid,
               std.stdrefid,
               stdlnm,
               stdfnm,
               " . IDEAParts::get('spedPeriod') . " as spedperiod,
               gl_code,
               vndname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
                       " . IDEAParts::get('studentJoin') . "
                       " . IDEAParts::get('gradeJoin') . "
                       " . IDEAParts::get('enrollJoin') . "
               INNER JOIN sys_vndmst ON sys_vndmst.vndrefid = std.vndrefid
         WHERE ADD_SEARCH
         ORDER BY UPPER(stdlnm), UPPER(stdfnm)
         LIMIT 25
    ";

    $list->addSearchField('Last Name', "LOWER(stdlnm)  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField('First Name', "LOWER(stdfnm)  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField('Lumen #', "std.stdrefid");
    $list->addSearchField('Student #', "LOWER(stdschid)  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField('Federal #', "LOWER(stdfedidnmbr)  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField('District', 'std.vndrefid', 'list')
		->sql("
            SELECT vndrefid,
		           vndname
              FROM sys_vndmst
             ORDER by 2
        ");
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
        ->value('A')
        ->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
        ->name('spedstatus');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

    $list->addColumn('Lumen ID');
    $list->addColumn('Last Name');
    $list->addColumn('First Name');
    $list->addColumn('Sp Ed Period');
    $list->addColumn('Grade');
    $list->addColumn('District');
    $list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus');
    $list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus');

    $list->editURL  = 'javascript:openWindow(AF_REFID, "AF_COL2", "AF_COL3")';
    $list->hideCheckBoxes = true;

    $list->printList();

?>
<script type='text/javascript'>
    function openWindow(refid, last, first) {
        var win = api.desktop.open(first + ' ' + last + ' - Student Utilities', api.url('std_tabs.php', {'tsRefID' : refid}));
        win.maximize();
        win.show();
    }
</script>
