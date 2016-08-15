<?php
	Security::init();

	$list = new listClass();

	$list->title = "Blocks Constructions";

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT c.cnrefid,
		       f.shortdesc,
		       c.cnname,
		       g.cgname,
		       c.file_defaults,
		       c.class_defaults,
		       length(c.cnbody) AS cbody,
		       'XML',
		       deactivation_date,
		       CASE WHEN NOW() > deactivation_date THEN 'N' ELSE 'Y' END AS deactivation
		  FROM webset.sped_constructions AS c
		       INNER JOIN webset.sped_menu_set AS f ON setrefid = srefid
			   LEFT OUTER JOIN webset.sped_constructions_group AS g ON c.group_id = g.cgrefid
		 WHERE (1=1) ADD_SEARCH
		 ORDER BY state, shortdesc, g.cgname, order_num, cnname, cndesc
	";

    $list->addSearchField('ID', "(cnrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
    $list->addSearchField('Name', "LOWER(cnname)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Body', "LOWER(cnbody)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(FFSelect::factory("IEP Format"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY state, shortdesc
		")
		->sqlField('srefid');
	$list->addSearchField(FFSelect::factory("Group"))
		->sql("
			SELECT cgrefid,
			       cgname
			  FROM webset.sped_constructions_group
			 WHERE (enddate IS NULL OR now()< enddate)
			 ORDER BY cgname
		")
		->sqlField('cgrefid');

	$list->addSearchField(
		FFSwitchAI::factory('Status')
			->sqlField("CASE WHEN NOW() > c.deactivation_date THEN 'I' ELSE 'A' END")
			->value('A')
	);

	$list->addColumn("IEP Format")->sqlField('shortdesc')->type('group');
	$list->addColumn("ID")->sqlField('cnrefid');
	$list->addColumn("Construction Name")->sqlField('cnname');
	$list->addColumn("Group")->sqlField('cgname');
	$list->addColumn("Default File")->sqlField('file_defaults');
	$list->addColumn("Default Class")->sqlField('class_defaults');
	$list->addColumn("Length")->sqlField('cbody');
	$list->addColumn("Preview")->dataCallback("editXml");
	$list->addColumn('Active')->hint('Status')->type('switch')->sqlField('deactivation');


	$list->addURL = CoreUtils::getURL('./constructions_edit.php');
	$list->editURL = CoreUtils::getURL('./constructions_edit.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_constructions')
			->setKeyField('cnrefid')
			->applyListClassMode()
	);


	$list->addButton(
		IDEAFormChecker::factory()
			->setTable('webset.sped_constructions')
			->setKeyField('cnrefid')
			->setNameField('cnname')
			->setXmlField('cnbody')
			->applyListClassMode()
	);

//	$list->addButton(FFButton::factory('Check Duplicates'))
//		->onClick("checkduples();");

	$list->addButton(FFButton::factory('Preview'))
		->onClick("previewXML('constructions');");

	$list->addRecordsResequence(
		'webset.sped_constructions',
		'order_num'
	);

	$list->printList();

	function editXml($data) {
		return UILayout::factory()
			->addObject(UIAnchor::factory("XML")->onClick('completeForm(AF_REFID, event)'))
			->newLine()
			->addObject(UIAnchor::factory("XML old")->onClick('editXml(AF_REFID, event)'))
			->toHTML();
	}

?>

<script>
	function editXml(RefID, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Form Edit and Test', api.url("<?= $g_virtualRoot; ?>/applications/webset/support/form.php?area=sped_constructions&form_id=" + RefID));
		win.resize(900, 700);
		win.show();
	}

	function completeForm(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Form Edit and Test', api.url('constructions_xml_edit.ajax.php', {'id': id}))
		win.resize(900, 700);
		win.addEventListener(WindowEvent.CLOSE, formCompleted);
		win.show();
	}

	function checkduples() {
		var res = ListClass.get().getSelectedValues().values.join(',') + ',';
		var win = api.window.open('Check', api.url("<?=$g_virtualRoot;?>/applications/webset/sys_maint/sped_menu/constructionsDupl.php?mode=constructions&forms=" + res));
		win.resize(900, 700);
		win.show();
	}

	function previewXML(area) {
		var res = ListClass.get().getSelectedValues().values.join(',') + ',';
		var win = api.window.open('Preview', api.url("<?=$g_virtualRoot;?>/applications/webset/sys_maint/sped_menu/previewXML.php?mode=" + area + "&forms=" + res));
		win.resize(900, 700);
		win.show();
	}

	function formCompleted() {
		api.reload();
	}
</script>
