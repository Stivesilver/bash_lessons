<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$list = new ListClass();

	$list->title = 'Evaluation Procedures';
	$list->showSearchFields = "yes";

	$list->SQL = "
        SELECT shsdrefid,
               scr.scrdesc,
               std.hsprefid,
               CASE WHEN lower(hspdesc) LIKE '%other%' THEN COALESCE(test_name, hspdesc) ELSE hspdesc END AS as_name,
               std.shsddate,
               std.screener,
               std.archived,
               std.location,
               std.order_num,
               std.xml_data
          FROM webset.es_std_scr std
               INNER JOIN webset.es_scr_disdef_proc ass ON std.hsprefid = ass.hsprefid
               INNER JOIN webset.es_statedef_screeningtype AS scr ON scr.scrrefid = ass.screenid
         WHERE stdrefid = " . $tsRefID . "
           AND eprefid = " . $evalproc_id . "
         ORDER BY scr.scrseq, order_num
    ";

	$list->addSearchField(FFSelect::factory('Status'))
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'Archived')")
		->sqlField("(CASE archived WHEN 'Y' THEN 2 ELSE 1 END)")
		->value(1);

	$list->addColumn('Area')->sqlField('scrdesc')->type('group');
	$list->addColumn('Name')->sqlField('as_name');
	$list->addColumn('Date')->sqlField('lastupdate')->sqlField('shsddate')->type('date');
	$list->addColumn('Person')->sqlField('screener');
	$list->addColumn('Location')->sqlField('location');
	$list->addColumn('Archived')->sqlField('archived');
	$list->addColumn('Form')
		->type('link')
		->align('center')
		->param('javascript:completeForm(AF_REFID, "' . $dskey . '")')
		->dataCallback('markCompletedForm');

	$list->addRecordsResequence('webset.es_std_scr', 'order_num');

	$list->addURL = CoreUtils::getURL('./eval_procedures_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./eval_procedures_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.es_std_scr';
	$list->deleteKeyField = 'shsdrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addRecordsProcess('[Un]Archive')
		->message('Do you really want to archive selected procedure?')
		->url(CoreUtils::getURL('./eval_procedures_arc.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->help('Archived Procedures will not be included in Evalution Report')
		->css('width', '80px');

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();

	print FormField::factory('hidden')
		->name('formcaption')
		->value($ds->safeGet('stdname') . ' - Assessment Form')
		->toHTML();

	function markCompletedForm($data, $col) {
		if ($data['xml_data'] != '') {
			return UILayout::factory()->addHTML('Completed', '[font-weight: bold;]') ->toHTML();
		} else {
			return 'Not completed';
		}
	}
?>
<script type="text/javascript">
	function completeForm(RefID, dskey) {
		var win = api.window.open(
			$("#formcaption").val(),
			api.url(
				'./eval_procedures_completer.php',
				{'RefID': RefID, 'dskey': dskey}
			)
		);
		win.maximize();
		win.addEventListener(WindowEvent.CLOSE, formCompleted);
		win.show();
	}

	function formCompleted() {
		api.reload();
	}
</script>
