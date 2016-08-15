<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Location';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT crtrefid,
                   crtdesc,                   
                   CASE WHEN NOW() > enddate  THEN 'N' ELSE 'Y' END  as status
              FROM webset.disdef_location
             WHERE vndrefid = VNDREFID 
                   ADD_SEARCH
            ORDER BY crtdesc
        ";

        $list->addSearchField('Location', "LOWER(crtdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Location');
        $list->addColumn('Active')->type('switch');

        $list->addURL = 'location.php';
        $list->editURL = 'location.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_location')
                ->setKeyField('crtrefid')
                ->applyListClassMode()
        );

        $list->printList();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Location';

        $edit->setSourceTable('webset.disdef_location', 'crtrefid');

        $edit->addGroup('General Information');
        $edit->addControl('Location', 'edit')->sqlField('crtdesc')->name('crtdesc')->size(90)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint('Such Location already exists', "
                SELECT 1 
                  FROM webset.disdef_location
                 WHERE vndrefid = VNDREFID
                   AND crtdesc = '[crtdesc]'
                   AND crtrefid != AF_REFID
        ");

        $edit->finishURL = 'location.php';
        $edit->cancelURL = 'location.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    }
?>
