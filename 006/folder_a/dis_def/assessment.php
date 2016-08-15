<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'District-wide Assessments';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT dwarefid,
                   dwadesc,
                   dwaseq,              
                   CASE WHEN NOW() > recdeactivationdt  THEN 'N' ELSE 'Y' END  as status
              FROM webset.disdef_assess
             WHERE vndrefid = VNDREFID 
                   ADD_SEARCH
            ORDER BY dwaseq, dwadesc
        ";

        $list->addSearchField('Assessment', "LOWER(dwadesc)  like '%' || LOWER('ADD_VALUE') || '%'");
        $list->addSearchField(
            FFIDEAStatus::factory()
                ->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END")
        );

        $list->addColumn('Assessment');
        $list->addColumn('Sequence');
        $list->addColumn('Active')->type('switch');

        $list->addURL = 'assessment.php';
        $list->editURL = 'assessment.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_assess')
                ->setKeyField('dwarefid')
                ->applyListClassMode()
        );

        $list->printList();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Assessment';

        $edit->setSourceTable('webset.disdef_assess', 'dwarefid');

        $edit->addGroup('General Information');
        $edit->addControl('Assessment', 'edit')->sqlField('dwadesc')->name('dwadesc')->size(90)->req();
        $edit->addControl('Sequence', 'integer')->sqlField('dwaseq')->size(10);
        $edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint('Such Assessment already exists', "
                SELECT 1 
                  FROM webset.disdef_assess
                 WHERE vndrefid = VNDREFID
                   AND dwadesc = '[dwadesc]'
                   AND dwarefid != AF_REFID
        ");

        $edit->finishURL = 'assessment.php';
        $edit->cancelURL = 'assessment.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    }
?>
