<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid = $ds->safeGet('stdrefid');
	$set_ini = IDEAFormat::getIniOptions();

//	$dskeyControl = FFInput::factory()->name('dskey')->value($dskey)->hide();

	$list = new ListClass();

	$list->title = $set_ini['iep_title'] . ' Documentation';

	$list->SQL = "
		SELECT sfrefid,
			   form_name,
			   siymiepbegdate,
			   siymiependdate,
			   std.lastupdate,
			   std.lastuser,
			   archived,
			   stt.frefid
		  FROM webset.std_forms_xml std
			   INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.tsrefid
			   INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
			   INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.mfcprefid
			   LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = std.iepyear
		 WHERE ts.stdrefid = " . $stdrefid . " " . (
				   IDEACore::disParam(50) == 'Y' ? " AND iepyear = " . $stdIEPYear : ""
			   ) . "
		 UNION ALL
		SELECT sfrefid,
			   'Uploaded Documents' || ': ' || uploaded_title AS form_name,
			   siymiepbegdate,
			   siymiependdate,
			   std.lastupdate,
			   std.lastuser,
			   archived,
			   NULL
		  FROM webset.std_forms_xml std
			   INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.tsrefid
			   LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = std.iepyear
		 WHERE ts.stdrefid = " . $stdrefid . " " . (
				   IDEACore::disParam(50) == 'Y' ? " AND iepyear = " . $stdIEPYear : ""
			   ) . "
		   AND uploaded_filename IS NOT NULL
		 ORDER BY sfrefid DESC
    ";

	$list->addSearchField('Form Title', 'form_name')->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('Title')
		->sqlField('form_name');
	$list->addColumn($set_ini['iep_year_title'])
		->dataCallback(
			create_function(
				'$data',
				'return CoreUtils::formatDateForUser($data["siymiepbegdate"]) . " - " . CoreUtils::formatDateForUser($data["siymiependdate"]);'
			)
		);
	$list->addColumn('Completed On')
		->type('date')
		->sqlField('lastupdate');
	$list->addColumn('Completed By')
		->sqlField('lastuser');
	$list->addColumn('Archived')
		->type('switch')
		->sqlField('archived');

	$list->multipleEdit = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_forms_xml')
			->setKeyField('sfrefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Archive')
		->message('Do you really want to archive selected forms?')
		->url(CoreUtils::getURL('frm_archive.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete selected forms?')
		->url(CoreUtils::getURL('frm_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->addURL =
		CoreUtils::getURL('frm_add.php', array(
				'dskey' => $dskey,
				'purpose' => (io::get('purpose') ? io::get('purpose') : null)
			)
		);

	$list->addRecordsProcess('Duplicate')
		->message('Do you really want to duplicate selected forms?')
		->url(CoreUtils::getURL('dublicate.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->css('width', '80px');

	$list->prepareRow('prepareRow');

	$list->addButton('Upload')
		->onClick('uploadForm(' . json_encode($dskey) . ')')
		->css('width', '80px');

//	$list->addHTML($dskeyControl->toHTML(), ListClassElement::RECORDS_UNDER);

	$list->printList();

	function prepareRow(ListClassRow $row) {
		global $dskey;
		$row->onClick('checkForm(' . $row->dataID . ', ' . json_encode($row->data['frefid']) . ', ' . json_encode($dskey) . ', ' . json_encode(CoreUtils::getVirtualPath('./frm_main.php')) . ')');
	}

	$cp = new SystemCompatibility();
//	if (IDEACore::disParam(116) != 'N') {
//		$link = IDEAAnnouncement::factory(152)
//			->getLink();
//		if ($cp->browserName == $cp::BROWSER_FIREFOX && $cp->browserVersion > 18) {
//			print UIMessage::factory('Can not Save or Print PDF ? Please make following ' . $link->getLink() , UIMessage::NOTE)->toHTML();
//		} elseif ($cp->browserName == $cp::BROWSER_CHROME) {
//			print UIMessage::factory('Can not Save or Print PDF ? Please make following <a href="javascript:messageShow(152);"><b>changes</b></a>.', UIMessage::NOTE)->toHTML();
//		}
//	}

?>

<script>
	function checkForm(RefID, stform, dskey, furl) {
		var url = api.url('form_check.ajax.php');
		api.ajax.post(
			url,
			{'RefID': RefID},
			function (answer) {
				if (answer.uploaded) {
					downloadForm(answer.RefID);
				} else {
					editForm(RefID, stform, dskey, furl);
				}
			}
		);
	}

	function editForm(RefID, stform, dskey, furl) {
		var win = api.window.open('Edit Form',
			api.url('./frm_xml.ajax.php'),
			{
				'std_id': RefID,
				'stateform': stform,
				'cancel_url': furl,
				'finish_url': furl,
				'dskey': dskey
			}
		);
		win.addEventListener('form_saved', onEvent);
		win.maximize();
		win.show();
	}

	function uploadForm(dskey) {
		var url = api.url('form_upload.php');
		url = api.url(url, {'dskey': dskey});
		var win = api.window.open('Upload Form', url);
		win.addEventListener('form_saved', onEvent);
		win.center();
		win.show();
	}

	function downloadForm(RefID) {
		api.ajax.process(UIProcessBoxType.PROCESSING, 'form_doc_download.ajax.php', {'RefID': RefID}).addEventListener(ObjectEvent.COMPLETE, onEvent);
	}

	function onEvent(e) {
		ListClass.get().reload();
	}
</script>
