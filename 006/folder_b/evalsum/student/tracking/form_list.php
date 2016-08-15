<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$list = new ListClass();

	$list->title = 'Evaluation Forms';

	$list->SQL = "
		SELECT frefid,
		       form_title,
		       to_char(std.lastupdate, 'mm-dd-yyyy'),
		       std.lastuser,
		       archived,
		       import_xml_id,
		       NULL AS doc_id
		  FROM webset.es_std_evalproc_forms std
		       INNER JOIN webset.es_disdef_evalforms dis ON dis.efrefid = std.evalforms_id
		 WHERE evalproc_id = " . $evalproc_id . "
		   AND (
				   std.pdf_cont IS NOT NULL
				OR std.xml_cont IS NOT NULL
		       )
		 UNION
		SELECT std.frefid,
		       stt.form_name || ' - [Imported]' AS form_title,
		       to_char(dforms.lastupdate, 'mm-dd-yyyy'),
		       dforms.lastuser,
		       dforms.archived,
			   import_xml_id,
			   stt.frefid AS doc_id
		  FROM webset.es_std_evalproc_forms std
		       INNER JOIN webset.std_forms_xml dforms ON std.import_xml_id = dforms.sfrefid
			   INNER JOIN webset.statedef_forms_xml stt ON dforms.frefid = stt.frefid
		 WHERE evalproc_id = " . $evalproc_id . "
		  UNION ALL
        SELECT std.frefid,
               std.uploaded_title,
               to_char(std.lastupdate, 'mm-dd-yyyy'),
               std.lastuser,
               std.archived,
               import_xml_id,
               -1 AS doc_id
          FROM webset.es_std_evalproc_forms std
         WHERE evalproc_id = " . $evalproc_id . "
           AND uploaded_filename IS NOT NULL
		 ORDER BY frefid DESC
		 ";

	$list->addSearchField('Form Title', 'form_title')->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('Title');
	$list->addColumn('Completed On')->type('date');
	$list->addColumn('Completed By');
	$list->addColumn('Archived')->type('switch');

	$list->multipleEdit = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_evalproc_forms')
			->setKeyField('frefid')
			->applyListClassMode()
	);

	$list->addButton('Upload')->onClick('uploadDoc();')->css('width', '80px');

	$list->addButton('Import')
		->css('width', '90px')
		->onClick('importDocForms()');

	$list->addRecordsProcess('Archive')
		->message('Do you really want to archive selected forms?')
		->url(CoreUtils::getURL('form_archive.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete selected forms?')
		->url(CoreUtils::getURL('form_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->addButton(
		FFButton::factory('Add New')
			->leftIcon('plus.png')
			->onClick('addForm();')
	);

	$list->editURL = "javascript:editForm('AF_REFID', 'AF_COL5', 'AF_COL6');";

	$list->printList();

	io::jsVar('dskey', $dskey);

?>
<script type="text/javascript">
	function importDocForms() {
		var wnd = api.window.open(
			'Import Documentation Forms',
			api.url(
				'form_import_list.php',
				{
					'dskey': dskey,
				}
			)
		);
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('forms_imported', onEvent);
		wnd.show();
	}

	function addForm() {
		var wnd = api.window.open(
			'Add Form',
			api.url(
				'./form_add.php',
				{
					'dskey': dskey,
				}
			)
		);
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('forms_imported', onEvent);
		wnd.show();
	}

	function editForm(frefid, imp_id, doc_id) {
		if (frefid > 0 || imp_id > 0) {

			if (doc_id == -1) {
				api.ajax.process(UIProcessBoxType.PROCESSING, 'form_doc_download.ajax.php', {'RefID': frefid})
			} else if (imp_id > 0) {
				var win = api.window.open(
					'Edit Form',
					api.url(
						'<?=SystemCore::$virtualRoot;?>/apps/idea/iep/evalforms_xml/frm_xml.ajax.php'),
					{
						'std_id': imp_id,
						'stateform': doc_id,
						'dskey': dskey
					}
				);
			} else {
				var win = api.window.open(
					'Edit Form',
					api.url(
						'./form_add.ajax.php'),
					{
						'dskey': dskey,
						'std_id': frefid
					}
				);
			}
			win.addEventListener('form_saved', onEvent);
			win.maximize();
			win.show();
		}
	}

	function uploadDoc() {
		var url = api.url('form_upload.php');
		url = api.url(url, {'dskey': dskey});
		var win = api.window.open('Upload Form', url);
		win.addEventListener('form_saved', onEvent);
		win.center();
		win.show();
	}

	function onEvent(e) {
		api.reload();
	}

</script>
