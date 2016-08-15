\<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Evaluation Forms';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT d.efrefid,
		       d.form_title,
		       CASE WHEN NOW() > d.recdeactivationdt THEN 'N' ELSE 'Y' END AS status,
		       'XML',
		       s.mfcrefid
		  FROM webset.es_disdef_evalforms AS d
		       LEFT OUTER JOIN webset.statedef_forms AS s ON s.mfcrefid = d.stateform_id
		 WHERE (1=1) ADD_SEARCH
		   AND d.vndrefid = VNDREFID
		 ORDER BY d.form_seq, d.form_title
    ";

	$list->addRecordsResequence(
		'webset.es_disdef_evalforms',
		'form_seq'
	);

	$list->addSearchField('Form', "LOWER(d.form_title)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Text in Body (XML)', "LOWER(encode(decode(d.form_xml, 'base64'),'escape'))  like '%' || LOWER('ADD_VALUE')|| '%'");
	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > d.recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Form Title')->sqlField('form_title');
	$list->addColumn('Status')->sqlField('status')->type('switch');
	$list->addColumn('Preview')->dataCallback('editXml')->width('10%');

	$list->addURL = './forms_edit.php';
	$list->editURL = './forms_edit.php';

	$list->addButton(FFIDEAExportButton::factory()
		->setTable('webset.es_disdef_evalforms')
		->setKeyField('efrefid')
		->applyListClassMode());

	$list->addButton(
		IDEAPopulateWindow::factory()
			->addNewItem()
			->setTitle('IEP Documentation Forms')
			->setSQL("
				SELECT f.mfcrefid,
					   p.mfcpdesc,
					   f.mfcdoctitle
				  FROM webset.statedef_forms as f
                       INNER JOIN webset.def_formpurpose as p ON  p.mfcprefid  = f.mfcprefid
					   LEFT OUTER JOIN webset.disdef_exceptions as e ON f.mfcrefid = e.statedef_id AND e.ex_area = 'orderpdf' AND e.vndrefid = VNDREFID
				 WHERE f.screfid=" . VNDState::factory()->id . "
				   AND f.mfcfilename IS NOT NULL
				   AND (
						   f.recdeactivationdt IS NULL
					OR now()< f.recdeactivationdt
					   )
				   AND COALESCE(f.onlythisip,'" . SystemCore::$VndName . "') LIKE '%" . SystemCore::$VndName . "%'
				 ORDER BY p.mfcpdesc, f.mfcdoctitle
			")
			->addSearch("Purpose", "mfcpdesc")
			->addSearch("Form", "lower(mfcdoctitle)  like '%' || lower(ADD_VALUE)|| '%'")
			->addColumn('Purpose', null, 'group')
			->addColumn('Form')
			->setDestinationTable('webset.es_disdef_evalforms')
			->setDestinationTableKeyField('efrefid')
			->setSourceTable('webset.statedef_forms')
			->setSourceTableKeyField('mfcrefid')
			->addPair('stateform_id', 'mfcrefid', TRUE)
			->addPair('vndrefid', SystemCore::$VndRefID, TRUE)
			->addPair('form_title', 'mfcdoctitle', TRUE)
			->addPair('lastuser', SystemCore::$userUID, FALSE)
			->addPair('lastupdate', 'NOW()', TRUE)
			->getPopulateButton()

	);
	$list->printList();

	function editXml($data) {
		$link = UILayout::factory();
		if ($data['mfcrefid'] != '' ) {
			$link->addObject(UIAnchor::factory('View PDF')->onClick('openPDF("' . CryptClass::factory()->encode(IDEAFormTemplatePDF::factory($data['mfcrefid'])->getTemplatePath()) . '", event)'));
		} else {
			if (SystemCore::$coreVersion == '1') {
				$link->newLine()->addObject(UIAnchor::factory('XML PC')->onClick('editXml(AF_REFID, event)'));
			} else {
				$link->addObject(UIAnchor::factory('XML Tablet')->onClick('completeForm(AF_REFID, event)'));
			}
		}
		return $link->toHTML();
	}
?>
<script>
	function openPDF(path, evt) {
		api.event.cancel(evt);
		api.ajax.process(UIProcessBoxType.REPORT, api.url('<?=CoreUtils::getURL('/apps/idea/library/download.php');?>'), {'path' : path})
	}

	function editXml(RefID, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Form Edit and Test', api.url('<?=SystemCore::$virtualRoot;?>/applications/webset/support/form.php?area=eval_track_xml&form_id=' + RefID));
		win.resize(900, 700);
		win.show();
	}

	function completeForm(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Form Edit and Test', api.url('./forms_edit.ajax.php', {'id': id}))
		win.resize(900, 700);
		win.addEventListener(WindowEvent.CLOSE, formCompleted);
		win.show();
	}

</script>

