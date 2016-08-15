<?php

    Security::init();

    $list = new listClass();

    $list->title = 'Exit Report Details ' . io::get('repdesc');
    $list->showSearchFields = true;
    $list->printable = true;

    $list->SQL = "
        SELECT t1.cdedrefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               'Age '||t1.stdage,
               t5.dccode || ' ' || t5.dcdesc AS dis,
               t6.seccode || ' ' || t6.secdesc AS ecat,
               to_char(exitdate,'MM/DD/YYYY'),
               " . IDEAParts::get('stdsex') . ",
               ethdesc,
               " . IDEAParts::get('stdname') . " AS stdname,
			   stdschid
          FROM webset.disrep_cdereportdtl AS t1
               LEFT OUTER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = t1.stdrefid
               LEFT OUTER JOIN webset.statedef_disablingcondition AS t5 ON t5.dcrefid = t1.majordisabilityrefid
               LEFT OUTER JOIN webset.statedef_exitcategories AS t6 ON t6.secrefid = t1.exitcategoryrefid
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('ethnicJoin') . "
               " . IDEAParts::get('schoolJoin') . "
         WHERE t1.cderefid = " . io::get('RefID') . "
               ADD_SEARCH
         ORDER BY vouname, stdage, dcdesc, secdesc, stdsex, ethdesc, stdname
    ";

    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFIDEAExitCodes::factory())
        ->sqlField('t1.exitcategoryrefid');
    $list->addSearchField(FFIDEADisability::factory())
        ->sqlField('t1.majordisabilityrefid');
    $list->addSearchField('Age', 'stdage', 'list')
        ->sql("
            SELECT DISTINCT stdage, stdage
              FROM webset.disrep_cdereportdtl
             WHERE cderefid = " . io::get('RefID') . "
             ORDER BY stdage
        ");

    $list->addColumn('School', '2%', 'group');
    $list->addColumn('Age', '2%', 'group');
    $list->addColumn('Primary Disability', '');
    $list->addColumn('Exit Category', '');
    $list->addColumn('Exit Date', '');
    $list->addColumn('Gender', '');
    $list->addColumn('Ethnic', '');
    $list->addColumn('Student', '');
    $list->addColumn('Student ID Number (Ext2)', '');

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disrep_cdereportdtl')
            ->setKeyField('cdedrefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
