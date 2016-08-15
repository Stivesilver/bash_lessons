<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Procedures/Assessments List';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT hsprefid,
		       scrdesc,
		       hspdesc,
		       length(hsp.xml_test) AS xml_length,
		       hsp.lastuser,
		       hsp.lastupdate,
		       CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END AS status,
		       'XML'
		  FROM webset.es_scr_disdef_proc AS hsp
		       INNER JOIN webset.es_statedef_screeningtype AS scr ON scr.scrrefid = hsp.screenid
		 WHERE (1=1) ADD_SEARCH
		   AND vndrefid = VNDREFID
		 ORDER BY scrdesc, hspdesc
    ";

    $list->addSearchField('ID', "(hsprefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFIDEAEvalScreenType::factory())->sqlField('hsp.screenid');
	$list->addSearchField('Form', "hspdesc  ILIKE '%' || ADD_VALUE || '%'");
	$list->addSearchField('Text in Body (XML)', "hsp.xml_test ILIKE '%' || ADD_VALUE || '%'");
	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('')->sqlField('scrdesc')->type('group');
	$list->addColumn('Form Title')->sqlField('hspdesc');
	$list->addColumn('Length')->sqlField('xml_length');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('datetime');
	$list->addColumn('Status')->sqlField('status')->type('switch');
	$list->addColumn('Edit')->dataCallback('editXml')->width('10%');

	$list->addURL = './procedures_edit.php';
	$list->editURL = './procedures_edit.php';

	$list->addButton(FFIDEAExportButton::factory()
		->setTable('webset.es_scr_disdef_proc')
		->setKeyField('hsprefid')
		->applyListClassMode());

	$list->addButton(
		IDEAGroupPopulate::factory()
			->applyListClassMode()
			->setTable('webset.es_scr_disdef_proc')
			->setKeyField('hsprefid')
			->setNameField('hspdesc')
			->setContField('xml_test')
			->addKeys('screenid')
			->addKeys('hspdesc')
	);

	$list->addButton(
		IDEAFormChecker::factory()
			->setTable('webset.es_scr_disdef_proc')
			->setKeyField('hsprefid')
			->setNameField('hspdesc')
			->setXmlField('xml_test')
			->applyListClassMode()
	);

	$list->printList();

	function editXml($data) {
		$link = UILayout::factory()
			->addObject(UIAnchor::factory('XML Tablet')->onClick('completeForm(AF_REFID, event)'));

		if (SystemCore::$coreVersion == '1') {
			$link->newLine()
				->addObject(UIAnchor::factory('XML PC')->onClick('editXml(AF_REFID, event)'));
		}
		return $link->toHTML();
	}

?>
<script>
	function editXml(RefID, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Form Edit and Test', api.url('<?=SystemCore::$virtualRoot;?>/applications/webset/support/form.php?area=ead_xml&form_id=' + RefID));
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

