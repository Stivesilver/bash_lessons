<?php
	Security::init();

	$hisrefid = io::geti('hisrefid');

	io::jsVar('hisrefid', $hisrefid);

	$list = new ListClass();

	$list->title = '504 Documentation';

	$list->SQL = "
        SELECT sfrefid,
               s.lastupdate,
               cname AS cname,
               fname AS fname,
               s.lastuser,
               archived,
               CASE WHEN is_fb = '1' THEN 'Y' ELSE 'N' END AS ftype
          FROM webset.std_fif_forms s
               INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
               LEFT OUTER JOIN webset.disdef_fif_form_category c ON f.fcrefid = c.fcrefid
         WHERE hisrefid = " . $hisrefid . "
         UNION ALL
        SELECT sfrefid,
               s.lastupdate,
               'Uploaded Documents' AS cname,
               uploaded_title AS fname,
               s.lastuser,
               archived,
               'N' AS ftype
          FROM webset.std_fif_forms s
         WHERE hisrefid = " . $hisrefid . "
           AND uploaded_content IS NOT NULL
         ORDER BY 2 DESC, sfrefid
    ";

	$list->addColumn('Date')->type('date')->sqlField('');
	$list->addColumn('Category')->sqlField('lastupdate');
	$list->addColumn('Form')->sqlField('fname');
	$list->addColumn('FB')->sqlField('ftype')->type('switch');
	$list->addColumn('Lastuser')->sqlField('lastuser');
	$list->addColumn('Archived')->sqlField('archived');

	$list->multipleEdit = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_fif_forms')
			->setKeyField('sfrefid')
			->applyListClassMode()
	);

	$list->addButton('Upload')
		->onClick('uploadForm(' . json_encode($hisrefid) . ')')
		->css('width', '80px');

	$list->addRecordsProcess('[Un]Archive')
		->message('Do you really want to archive selected forms?')
		->url(CoreUtils::getURL('form_archive.ajax.php', array('hisrefid' => $hisrefid)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

//	$list->addButton(
//		FFButton::factory()
//			->caption('Print')
//			->onClick('print()')
//			->width(80)
//	);

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete selected forms?')
		->url(CoreUtils::getURL('form_delete.ajax.php', array('hisrefid' => $hisrefid)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->editURL = 'javascript:checkForm(AF_REFID)';

	$list
		->addButton('Add New')
		->width('80px')
		->balloon(
			UIBalloon::factory()
				->showInTopFrame(false)
				->addHTML('Add New Document')
				->addObject(
					UILayout::factory()
						->newLine()
						->addObject(
							FFSelect::factory('')
								->name('frefid')
								->sql("
						            SELECT frefid,
								           cname || ' / ' || fname
								      FROM webset.disdef_fif_forms f
								           LEFT OUTER JOIN webset.disdef_fif_form_category c ON f.fcrefid = c.fcrefid
								     WHERE f.vndrefid = VNDREFID
								           AND (f.enddate IS NULL OR NOW() < f.enddate)
								     ORDER BY cname, f.seqnum, fname
								")
								->emptyOption(true)
								->onChange('addNew()')
						)
				)
		);

	$list->printList();
?>
<script type="text/javascript">
	function checkForm(RefID) {
		var url = api.url('form_check.ajax.php');
		api.ajax.post(
			url,
			{'RefID': RefID},
			function (answer) {
				if (answer.uploaded) {
					downloadForm(answer.RefID);
				} else {
					editForm(answer.RefID);
				}
			}
		);
	}

	function editForm(RefID) {
		var win = api.window.open('Edit Form',
			api.url('./form_xml.ajax.php'),
			{
				'sfrefid': RefID,
				'hisrefid': hisrefid
			}
		);
		win.addEventListener('form_saved', onEvent);
		win.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				ListClass.get().reload();
			},
			this
		);
		win.maximize();
		win.show();
	}

	function downloadForm(RefID) {
		api.ajax.process(UIProcessBoxType.PROCESSING, 'form_doc_download.ajax.php', {'RefID': RefID});
	}

	function uploadForm(hisrefid) {
		var url = api.url('form_upload.php');
		url = api.url(url, {'hisrefid': hisrefid});
		var win = api.window.open('Upload Form', url);
		win.addEventListener('form_saved', onEvent);
		win.center();
		win.show();
	}

	function onEvent(e) {
		ListClass.get().reload();
	}

	function addNew() {
		var win = api.window.open('Edit Form',
			api.url('./form_xml.ajax.php'),
			{
				'sfrefid': 0,
				'hisrefid': hisrefid,
				'frefid': $('#frefid').val()
			}
		);
		win.addEventListener('form_saved', onEvent);
		win.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				ListClass.get().reload();
			},
			this
		);
		win.maximize();
		win.show();
	}

	function print() {
		var selected = ListClass.get().getSelectedValues().values.join(',');
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('./print_selected.ajax.php', {RefIDs: selected})
		);
	}
</script>
