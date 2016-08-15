<?php
	Security::init();
	$titles = json_decode(IDEAFormat::getIniOptions('demo_titles'), true);

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
               CASE
               WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
               ELSE 'N'
               END AS stdstatus
          FROM webset.dmg_studentmst std
               LEFT OUTER JOIN c_manager.def_grade_levels grd ON grd.gl_refid = std.gl_refid
               LEFT OUTER JOIN sys_voumst ON sys_voumst.vourefid = std.vourefid
         WHERE std.vndrefid = VNDREFID ADD_SEARCH
         ORDER BY UPPER(stdlnm), UPPER(stdfnm)
    ";

	$list->addSearchField(
		FFStudentName::factory()
			->caption($titles['student'] . ' Name')
	);

	$list->addSearchField('Guardian Last Name')
		->sqlField("
            EXISTS (
					SELECT 1
					  FROM webset.dmg_guardianmst grd
					 WHERE grd.stdrefid = std.stdrefid
					   AND LOWER(gdlnm) LIKE LOWER(ADD_VALUE)
                   )
			");

	$list->addSearchField($titles['student'] . ' #', 'stdschid');
	$list->addSearchField('Federal #', 'stdfedidnmbr');
	$list->addSearchField('State #', 'stdstateidnmbr');
	$list->addSearchField('Attending School', 'std.vourefid', 'list')
		->sql('
			SELECT vourefid,
				   vouname
			  FROM public.sys_voumst
			 WHERE vndrefid = VNDREFID
			 ORDER BY vouname
        ');

	$list->addSearchField(FFSwitchAI::factory($titles['student'] . ' Status'), 'stdstatus')->value('A');
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addColumn('Lumen ID');
	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('Middle Name');
	$list->addColumn('Grade');
	$list->addColumn('Attending School');
	$list->addColumn('Status')->type('switch');

	$list->addURL = 'javascript:openWindow(0)';
	$list->editURL = 'javascript:openWindow(AF_REFID)';

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.dmg_studentmst')
		->setKeyField('stdrefid')
		->applyListClassMode()
	);

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete selected ' . $titles['students'] . '?')
		->url('dmg_delete.ajax.php')
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();
?>
<script type='text/javascript'>
	function openWindow(refid) {
		var win = api.desktop.open('Loading...', api.url('dmg_add.php', {'RefID': refid}));
		win.maximize();
		win.show();
	}
</script>
