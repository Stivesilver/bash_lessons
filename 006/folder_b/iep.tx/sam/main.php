<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudentTX::factory($tsRefID);

	$list = new ListClass();

	$list->title = 'Texas Assessment Program';

	$list->SQL = "
		SELECT samrefid,
			   dsydesc,
			   begdate,
			   samdesc,
			   COALESCE(gl_code, '" . db::escape($student->get('grdlevel')) . "'),
			   'STAAR',
			   'TELPAS',
			   'Additional',
			   COALESCE(ardinclude, 'N') as ardinclude
		  FROM webset_tx.std_sam_main sam
			   LEFT OUTER JOIN webset.disdef_schoolyear sy ON sy.dsyrefid = sam.syrefid
			   LEFT OUTER JOIN c_manager.def_grade_levels grd ON grd.gl_refid = sam.grade_id
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY begdate desc, samrefid desc
    ";

	$list->addColumn('School Year');
	$list->addColumn('Date')->type('date');
	$list->addColumn('Description');
	$list->addColumn('Grade');

	$list->addColumn('STAAR')
		->type('link')
		->align('center')
		->sortable(false)
		->param('javascript:openwin("AF_COL5", "AF_REFID", "AF_COL1", "AF_COL2", "AF_COL9")');

//	$list->addColumn('TAKS')
//		->type('link')
//		->align('center')
//	    ->sortable(false)
//		->param('javascript:openwin("AF_COL6", "AF_REFID", "AF_COL1", "AF_COL2", "AF_COL10")');

	$list->addColumn('TELPAS')
		->type('link')
		->align('center')
		->sortable(false)
		->param('javascript:openwin("AF_COL6", "AF_REFID", "AF_COL1", "AF_COL2", "AF_COL9")');

	$list->addColumn('Additional')
		->type('link')
		->align('center')
		->sortable(false)
		->param('javascript:openwin("AF_COL7", "AF_REFID", "AF_COL1", "AF_COL2", "AF_COL9")');

	$list->addColumn('Include in ARD')->type('switch');

	$list->addURL = CoreUtils::getURL('main_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('main_add.php', array('dskey' => $dskey));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_main')
			->setKeyField('samrefid')
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete this Assessment(s)?')
		->url(CoreUtils::getURL('main_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

	print FFInput::factory()
			->name('dskey')
			->value($dskey)
			->hide()
			->toHTML();

	print FFInput::factory()
			->name('stdname')
			->value($student->get('stdname'))
			->hide()
			->toHTML();

?>
<script type="text/javascript">
		function openwin(mode, samrefid, school_year, sam_date, include) {
			switch (mode) {
				case 'STAAR':
					url = 'staar_main.php';
					break;
				case 'TAKS':
					url = 'taks_main.php';
					break;
				case 'TELPAS':
					url = 'telpas_main.php';
					break;
				case 'Additional':
					url = 'additional_main.php';
					break;
			}
			title = mode + ' - ' + school_year + ' / ' + sam_date + ' - ' + $('#stdname').val();
			win = api.window.open(title, api.url(url, {'samrefid': samrefid, 'dskey': $('#dskey').val()}))
			win.maximize();
			win.show();
		}
</script>
