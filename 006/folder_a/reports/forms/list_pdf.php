<?php

    Security::init();

    $list = new listClass();
    $list->title = 'Documentation Mass Print';
    $list->showSearchFields = true;

    $list->SQL = "
        SELECT smfcrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
               vouname,
               gl_code,
               mfcdoctitle || CASE WHEN now() > state.recdeactivationdt THEN ' - [Deactivated Version]' ELSE '' END,
               forms.lastuser,
               forms.lastupdate
          FROM webset.sys_teacherstudentassignment ts
          	   " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('repSchoolJoin') . "
               INNER JOIN webset.std_forms forms ON ts.tsrefid = forms.stdrefid
               INNER JOIN webset.statedef_forms state ON forms.mfcrefid = state.mfcrefid
               INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
         WHERE std.vndrefid = VNDREFID
           AND xml_field_links IS NOT NULL
         ORDER BY 2, forms.lastupdate desc
    ";

    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFIDEASchool::factory(true))->name('vourefid');
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

    $list->addSearchField('Form', 'forms.mfcrefid', 'list')
        ->sql("
        	SELECT mfcrefid, mfcpdesc || ' -> ' || mfcdoctitle || CASE WHEN now() > recdeactivationdt THEN ' - [Deactivated Version]' ELSE '' END
                  FROM webset.statedef_forms
                       INNER JOIN webset.def_formpurpose ON webset.statedef_forms.mfcprefid =  webset.def_formpurpose.mfcprefid
                 WHERE (screfid = " . VNDState::factory()->id . ")
                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                   AND COALESCE(onlythisip,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
                   AND mfcrefid not in (SELECT statedef_id
                                          FROM webset.disdef_exceptions
                                         WHERE vndrefid = VNDREFID
                                           AND ex_area = 'document')
                    OR EXISTS (SELECT 1
                                 FROM webset.std_forms forms
                                      INNER JOIN webset.sys_teacherstudentassignment ts ON forms.stdrefid = ts.tsrefid
          	                          " . IDEAParts::get('studentJoin') . "
                                WHERE forms.mfcrefid = webset.statedef_forms.mfcrefid
                                  AND std.vndrefid = VNDREFID)
                 ORDER BY mfcpdesc, mfcdoctitle
        ");
    $list->addSearchField('Last User', "LOWER(forms.lastuser)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField("Dates Range", "forms.lastupdate", "date_range");

    $list->addColumn('Student Name');
    $list->addColumn('School');
    $list->addColumn('Grade');
    $list->addColumn('Form');
    $list->addColumn('Last User');
    $list->addColumn('Last Update')->type('date');

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_forms')
            ->setKeyField('smfcrefid')
            ->applyListClassMode()
    );

	$list->addButton(
		FFPrintButton::factory('./list_pdf_print.ajax.php', FFPrintButton::PDF)
			->value('Print Selected')
	);

	$list->hideCheckBoxes = false;

//    $list->addRecordsProcess('Print Selected')
//        ->message('Do you really want to print selected forms?')
//        ->url('list_pdf_print.ajax.php')
//        ->type(ListClassProcess::REPORT)
//        ->progressBar(true);

    $list->printList();
?>