<?php
    Security::init();
    
    if (io::get('RefID')=='') {
        
        $list = new ListClass();
        
        $list->title = 'Amount';
        
        $list->showSearchFields = true;

        $list->SQL = "
            SELECT sarefid,
                   sadesc,
                   seqnum,
                   CASE WHEN NOW() > enddate  THEN 'In-Active' ELSE 'Active' END  as status
              FROM webset.def_spedamt
             WHERE vndrefid = VNDREFID 
                   ADD_SEARCH
             ORDER BY seqnum, sadesc
        ";

        $list->addSearchField('Status', '', 'list')
            ->value('1')
            ->sqlField( '(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
            ->data(array(1 => 'Active', 2 => 'Inactive'));
            
        $list->addSearchField('Amount', "LOWER(sadesc)  like '%' || LOWER('ADD_VALUE') || '%'");
        
        
        $list->addColumn('Amount');
        $list->addColumn('Sequence');
        $list->addColumn('Status');

        $list->addURL  = 'amount.php';
        $list->editURL = 'amount.php';
        
        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.def_spedamt')
                ->setKeyField('sarefid')
                ->applyListClassMode()
        );

        $list->printList();
        
    } else {
        
        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Amount';
        
        $edit->setSourceTable('webset.def_spedamt', 'sarefid');

        $edit->addGroup('General Information');
        $edit->addControl('Amount')->sqlField('sadesc')->name('sadesc')->req();
        $edit->addControl('Sequence', 'integer')->sqlField('seqnum')->size(10);
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
        
        $edit->addSQLConstraint('Such Amount already exists', 
            "
            SELECT 1 
              FROM webset.def_spedamt
             WHERE vndrefid = VNDREFID
               AND sadesc = '[sadesc]'
               AND sarefid!=AF_REFID
        ");

        $edit->finishURL = 'amount.php';
        $edit->cancelURL = 'amount.php';
        
        $edit->firstCellWidth  = "30%";

        $edit->printEdit();        
    }
?>
