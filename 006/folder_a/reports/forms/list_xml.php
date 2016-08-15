<?php

    Security::init();

    $list = new listClass();
    $list->title = 'Documentation Mass Print';
    $list->showSearchFields = true;

    $list->SQL = "
        SELECT sfrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
               vouname,
               gl_code,
               form_name || CASE WHEN now() > state.end_date THEN ' - [Deactivated Version]' ELSE '' END,
               forms.lastuser,
               forms.lastupdate
          FROM webset.sys_teacherstudentassignment ts
          	   " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('repSchoolJoin') . "
               INNER JOIN webset.std_forms_xml forms ON ts.tsrefid = forms.stdrefid
               INNER JOIN webset.statedef_forms_xml state ON forms.frefid = state.frefid
         WHERE std.vndrefid = VNDREFID
         ORDER BY 2, forms.lastupdate desc
    ";

    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFIDEASchool::factory(true));
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

    $list->addSearchField('Form', 'forms.frefid', 'list')
        ->sql("
        	SELECT frefid, mfcpdesc || ' -> ' || form_name || CASE WHEN now() > end_date THEN ' - [Deactivated Version]' ELSE '' END
                  FROM webset.statedef_forms_xml
                       INNER JOIN webset.def_formpurpose purp ON form_purpose = mfcprefid
                 WHERE (screfid = " . VNDState::factory()->id . ")
                   AND (end_date IS NULL or now()< end_date)
                   AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
                   AND frefid not in (SELECT statedef_id
                                          FROM webset.disdef_exceptions
                                         WHERE vndrefid = VNDREFID
                                           AND ex_area = 'doc_xml')
                    OR EXISTS (SELECT 1
                                 FROM webset.std_forms_xml forms
                                      INNER JOIN webset.sys_teacherstudentassignment ts ON forms.stdrefid = ts.tsrefid
          	                          " . IDEAParts::get('studentJoin') . "
                                WHERE forms.sfrefid = webset.statedef_forms_xml.frefid
                                  AND std.vndrefid = VNDREFID)
                 ORDER BY mfcpdesc, form_name
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
            ->setTable('webset.std_forms_xml')
            ->setKeyField('sfrefid')
            ->applyListClassMode()
    );

    $list->addRecordsProcess('Print Selected')
        ->message('Do you really want to print selected forms?')
        ->url('list_xml_print.ajax.php')
        ->type(ListClassProcess::REPORT)
        ->progressBar(true);

    $list->printList();
?>