<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Method for Informing Parents of Progress';

        $list->SQL = "
            SELECT miprefid, 
                   mipdesc
              FROM webset.statedef_methodinfo
             WHERE vndrefid = VNDREFID
                   ADD_SEARCH 
             ORDER BY mipdesc
        ";

        $list->addColumn('Method');

        $list->addURL = 'srv_meth_info.php';
        $list->editURL = 'srv_meth_info.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.statedef_methodinfo')
                ->setKeyField('miprefid')
                ->applyListClassMode()
        );

        $list->printList();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Method for Informing Parents of Progress';

        $edit->setSourceTable('webset.statedef_methodinfo', 'miprefid');

        $edit->addGroup('General Information');
        $edit->addControl("Description", "textarea")
            ->sqlField('mipdesc')
            ->css("WIDTH", "100%")
            ->css("HEIGHT", "100px")
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint('This method already exists', "
            SELECT 1 
              FROM webset.statedef_methodinfo
             WHERE vndrefid = VNDREFID
               AND (mipdesc = '[mipdesc]')
               AND miprefid!=AF_REFID
        ");

        $edit->finishURL = 'srv_meth_info.php';
        $edit->cancelURL = 'srv_meth_info.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    }
?>
