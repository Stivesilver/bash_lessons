<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdrefid = $ds->safeGet('stdrefid');
	$tsRefID = $ds->safeGet('tsRefID');
	$title = IDEADocumentType::factory(io::get('doc'))->getTitle();

	$list = new ListClass();

	$list->title = $title . ' Builder';

	$list->addURL = CoreUtils::getURL('./builder_edit.php', array('dskey' => $dskey, 'idBlock' => io::get('doc')));
	$list->editURL = "javascript:api.ajax.process(UIProcessBoxType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/eval_view.ajax.php') . "', {'RefID' : AF_REFID}))";
	$list->multipleEdit = true;

	$where = "";
	if (!(SystemCore::$AccessType == "1" and IDEACore::disParam(90) != "N")) {
		$where = "AND deleted is NULL";
	}

	$list->SQL = "
		SELECT esarefid,
               to_char(esadate, 'mm-dd-YYYY'),
               COALESCE(doctype, eval.esaname) || CASE deleted WHEN 'Y' THEN ' - <font color=red>disabled</red>' ELSE '' END,
               eval.lastuser
          FROM webset.es_std_esarchived eval
               INNER JOIN webset.sys_teacherstudentassignment ts ON eval.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.sped_doctype AS dt ON eval.doc_id = dt.drefid
         WHERE ts.stdrefid = $stdrefid
               " . $where . "
         ORDER BY eval.lastupdate DESC
        ";

	$list->addColumn("Archive Date")->width('10%');
	$list->addColumn("Type of Document")->width('80%');
	$list->addColumn("Archived By")->width('10%');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_esarchived')
			->setKeyField('esarefid')
			->applyListClassMode()
	);

	$list->addButton('Upload')
		->onClick('uploadForm(' . json_encode($dskey) . ')')
		->css('width', '80px');

    if (SystemCore::$AccessType == "1") {
		$list->addRecordsProcess('Disable')
			->width('80px')
			->message('Do you really want to Disable selected records?')
			->url(CoreUtils::getURL('./builder_disable.ajax.php', array('dskey' => $dskey)))
			->type(ListClassProcess::DATA_UPDATE)
			->progressBar(false);
	}

	$list->printList();

?>

<script>
	function uploadForm(dskey) {
		var url = api.url('form_upload.php');
		url = api.url(url, {'dskey': dskey});
		var win = api.window.open('Upload Form', url);
		win.addEventListener('form_saved', onEvent);
		win.center();
		win.show();
	}

	function onEvent(e) {
		ListClass.get().reload();
	}
</script>
