<?php
	Security::init();

	$pcrefid = io::geti('pcrefid');

	$list = new ListClass();

	$list->SQL = "
        SELECT pcsarefid,
               t2.umlastname || ' ' || t2.umfirstname,
               t3.vouname
          FROM webset.sys_casemanagermst AS t1
               INNER JOIN public.sys_usermst AS t2 ON t2.umrefid = t1.umrefid
               INNER JOIN public.sys_voumst AS t3 ON t3.vourefid = t2.vourefid
               INNER JOIN webset.sys_proccoordassignment pca ON t1.umrefid = pca.cmrefid
         WHERE pca.pcrefid = " . $pcrefid . "
               ADD_SEARCH
         ORDER BY LOWER(t2.umlastname), LOWER(t2.umfirstname)
    ";

	$list->addSearchField("Building", "t3.vourefid", "list")
		->sql("
                SELECT vourefid,
                       vouname
                  FROM public.sys_voumst
                 WHERE vndrefid = VNDREFID
                 ORDER BY vouname
        ");

	$list->addSearchField(FFUserName::factory());

	$list->addColumn('Case Manager');
	$list->addColumn('School');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_proccoordassignment')
			->setKeyField('pcsarefid')
			->applyListClassMode()
	);

	$list->deleteTableName = 'webset.sys_proccoordassignment';
	$list->deleteKeyField = 'pcsarefid';

	$list->addButton('Add Case Manager(s)')
		->onClick('selectStudent(' . $pcrefid . ')');

	$list->printList();

?>

<script type="text/javascript">
	function selectStudent(pcrefid) {
		var wnd = api.window.open('Add Case Manager(s)', api.url('pc_assigm_sel.php', {
			'umrefid': $('#umrefid').val(),
			'pcrefid': pcrefid
		}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('student_assigned', onEvent);
		wnd.show();
	}

	function onEvent(e) {
		api.reload();
	}

</script>
