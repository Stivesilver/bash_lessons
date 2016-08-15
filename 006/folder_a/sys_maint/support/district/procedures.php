<?php
    Security::init();

    $list = new ListClass();

    $list->title = 'District Procedures';
    $list->showSearchFields = true;

    $list->SQL = "
        SELECT hsprefid,
               vndname,
               scrdesc,
               hspdesc,
               length(xml_test),
               proc.lastuser,
               proc.lastupdate,
               CASE WHEN NOW() > proc.recdeactivationdt THEN 'N' ELSE 'Y' END AS status,
               'XML'
          FROM webset.es_scr_disdef_proc proc
               INNER JOIN webset.es_statedef_screeningtype sarea ON sarea.scrrefid = proc.screenid
               INNER JOIN sys_vndmst vnd ON proc.vndrefid = vnd.vndrefid
         WHERE ADD_SEARCH
           AND xml_test IS NOT NULL
         ORDER BY vndname, scrdesc, hspdesc

    ";

	$list->addSearchField("ID", "(hsprefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
    $list->addSearchField('Vndrefid', "(proc.vndrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
    $list->addSearchField('District', "LOWER(vndname)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Area', 'sarea.scrrefid', 'select')
        ->sql("
			SELECT scrrefid,
	               scrdesc
	          FROM webset.es_statedef_screeningtype                   
	         WHERE screfid = " . VNDState::factory()->id . "	           
	         ORDER BY scrseq, 2
		");
    $list->addSearchField('Form', "LOWER(hspdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField("Text in Body (XML)", "LOWER(encode(decode(xml_test, 'base64'),'escape'))  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField(FFIDEAStatus::factory())->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END");

	$list->addColumn('ID')->sqlField('hsprefid');
    $list->addColumn('District', '', 'group');
    $list->addColumn('Area', '', 'group');
    $list->addColumn('Procedure');
    $list->addColumn('Length');
    $list->addColumn('Last User');
    $list->addColumn('Last Update');
    $list->addColumn('Active')->type('switch')->sqlField('status');

    $list->editURL = 'javascript:edit_form(AF_REFID)';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.es_scr_disdef_proc')
            ->setKeyField('hsprefid')
            ->applyListClassMode()
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
?>
<script type="text/javascript">
    function edit_form(id) {
        var wnd = api.window.open('Edit Form', api.url('../form/form.php', {'form_id': id, 'area': 'ead_xml'}));
        wnd.resize(950, 600);
        wnd.center();
        wnd.show();
    }
</script>
