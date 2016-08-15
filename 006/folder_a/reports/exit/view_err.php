<?php

    Security::init();

    $list = new listClass();

    $list->title = 'Exit Report Error Log ' . io::get('repdesc');
    $list->showSearchFields = true;
    $list->printable = true;

    $dt = db::execSQL("
        SELECT TO_CHAR(dateofrep, 'MM/DD/YYYY')
          FROM webset.disrep_cdereportmst
         WHERE cderefid = " . io::get('RefID') . "
    ")->getOne();

    $list->SQL = "
        SELECT t1.cdeerefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               'Age '||EXTRACT(YEAR FROM AGE(TO_DATE('" . $dt . "', 'MM/DD/YYYY'), std.stddob)) AS stdage,
               " . IDEAParts::get('stdname') . " AS stdname,
               " . IDEAParts::get('username') . " AS cmname,
               " . IDEAParts::get('disabcode') . " AS dias,
               t10.spccode AS plac,
               t7.seccode || ' ' || t7.secdesc AS ecat,
               " . IDEAParts::get('stdsex') . " AS gender,
               ethdesc,
               t1.errdesc
          FROM webset.disrep_cdereporterr AS t1
               INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = t1.stdrefid
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('ethnicJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . IDEAParts::get('casemanJoin') . "
               LEFT OUTER JOIN webset.statedef_exitcategories AS t7 ON t7.secrefid = ts.exitrefid
               LEFT OUTER JOIN webset.std_placementcode AS t9 ON t9.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.statedef_placementcategorycode AS t10 ON t10.spcrefid = t9.spcrefid
         WHERE t1.cderefid = " . io::get('RefID') . "
               ADD_SEARCH
         ORDER BY vouname, CAST(EXTRACT(YEAR FROM AGE(TO_DATE('" . $dt . "', 'MM/DD/YYYY'), std.stddob)) AS INTEGER), stdname, secdesc, stdsex, ethdesc, errdesc
    ";

    $list->addSearchField('Error Type', 't1.errdesc', 'list')
        ->sql("
            SELECT DISTINCT
                   errdesc,
                   errdesc
              FROM webset.disrep_cdereporterr
             WHERE cderefid = " . io::get('RefID') . "
             ORDER BY 1
        ");
    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFUserName::factory())
        ->caption('Case Manager');

    $list->addColumn('School', '2%', 'group');
    $list->addColumn('Age', '2%', 'group');
    $list->addColumn('Student');
    $list->addColumn('Case Manager');
    $list->addColumn('Disability');
    $list->addColumn('Placement');
    $list->addColumn('Exit Category');
    $list->addColumn('Gender');
    $list->addColumn('Ethnic');
    $list->addColumn('Error Message');

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disrep_cdereporterr')
            ->setKeyField('cdeerefid')
            ->applyListClassMode()
    );

    $list->printList();
?>