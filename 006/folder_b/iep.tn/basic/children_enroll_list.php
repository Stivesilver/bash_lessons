<?php
	Security::init();

	$set_ini = IDEAFormat::getIniOptions();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
        SELECT stdrefid,
               stdrefid,
               stdlnm,
               stdfnm,
               stdmnm,
               gl_code,
               vouname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN EXISTS (SELECT 1 FROM webset.sys_teacherstudentassignment ts WHERE std.stdrefid = ts.stdrefid AND " . IDEAParts::get('spedActive') . ") THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.dmg_studentmst std
               LEFT OUTER JOIN c_manager.def_grade_levels grd ON grd.gl_refid = std.gl_refid
               LEFT OUTER JOIN sys_voumst ON sys_voumst.vourefid = std.vourefid
         WHERE std.vndrefid = VNDREFID
               ADD_SEARCH
         ORDER BY UPPER(stdlnm), UPPER(stdfnm)
    ";

	$list->addSearchField('Last Name', 'stdlnm');
	$list->addSearchField('First Name', 'stdfnm');
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField('Student #', 'stdschid');
	$list->addSearchField('Federal #', 'stdfedidnmbr');
	$list->addSearchField('State #', 'stdstateidnmbr');
	$list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
	$list->addSearchField(FFSwitchAI::factory('IFSP Status'))
		->name('spedstatus')
		->sqlField("CASE WHEN EXISTS (SELECT 1
                                        FROM webset.sys_teacherstudentassignment ts 
                                       WHERE std.stdrefid = ts.stdrefid 
                                         AND " . IDEAParts::get('spedActive') . ") THEN 'A'
                         ELSE 'I' 
                    END");
	$list->addSearchField(FFIDEAEnrollCodes::factory())
		->sqlField("EXISTS (SELECT 1 FROM webset.sys_teacherstudentassignment ts WHERE std.stdrefid = ts.stdrefid AND denrefid = ADD_VALUE)")
		->name('denrefid');
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid')->name('gl_refid');

	$list->addColumn('Lumen ID');
	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('Middle Name');
	$list->addColumn('Grade');
	$list->addColumn('Attending School');
	$list->addColumn('Status')->hint('Student Status')->type('switch')->sqlField('stdstatus');
	$list->addColumn($set_ini['iep_title'] . ' Status')->hint($set_ini['iep_title'] . ' Status')->type('switch')->sqlField('spedstatus');

	$list->editURL = 'javascript:openWindow(AF_REFID)';

	$list->printList();

?>
<script type='text/javascript'>
	function openWindow(refid) {
		var win = api.desktop.open('Loading...', api.url('enr_history.php', {'stdrefid': refid}));
		win.maximize();
		win.show();
	}
</script>
