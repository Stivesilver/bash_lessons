<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'School Bell to Bell minutes';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT refid,
                   vouname,
                   validvalue as minutes
              FROM webset.disdef_validvalues vv
                   INNER JOIN sys_voumst vou ON vv.vourefid = vou.vourefid 
             WHERE vou.vndrefid = VNDREFID 
               AND valuename = 'BellToBellMinutes'	
                   ADD_SEARCH
             ORDER BY 2
        ";

        $list->addSearchField('School', '', 'list')
            ->sqlField('vv.vourefid')
            ->sql("
            	SELECT vourefid, vouname
            	  FROM sys_voumst
            	 WHERE vndrefid = VNDREFID
            	 ORDER BY 1 
            ");

        $list->addColumn('School');
        $list->addColumn('Bell to Bell Minutes');

        $list->addURL = 'srv_belltobell.php';
        $list->editURL = 'srv_belltobell.php';

        $list->deleteKeyField = 'refid';
        $list->deleteTableName = 'webset.disdef_validvalues';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->printList();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit School Bell to Bell minutes';

        $edit->setSourceTable('webset.disdef_validvalues', 'refid');

        $edit->addGroup('General Information');
        $edit->addControl('School', 'list')
            ->name('vourefid')
            ->sqlField('vourefid')
            ->sql("
            	SELECT vourefid, vouname
            	  FROM sys_voumst
            	 WHERE vndrefid = VNDREFID
            	 ORDER BY 1 
            ")
            ->req();

        $edit->addControl('Bell to Bell minutes', 'integer')
            ->sqlField('validvalue')
            ->size(10)
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Value Name', 'hidden')->value('BellToBellMinutes')->sqlField('valuename');

        $edit->addSQLConstraint('Setting for this school already exists', "
            SELECT 1 
              FROM webset.disdef_validvalues
             WHERE valuename = 'BellToBellMinutes'
               AND (vourefid = '[vourefid]')
               AND refid!=AF_REFID
        ");

        $edit->finishURL = 'srv_belltobell.php';
        $edit->cancelURL = 'srv_belltobell.php';

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    }
?>