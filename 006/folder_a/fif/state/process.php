<?php
    Security::init();

    $RefID = io::get('RefID');
    $staterefid = io::geti('staterefid');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit System 504 Process';

        $edit->setSourceTable('webset.def_fif_process', 'fiprefid');

        $edit->addGroup('General Information');

        $edit->addControl('504 Process')->sqlField('fipdesc')->name('fipdesc')->size(80)->req();        
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('State ID', 'hidden')->value($staterefid)->sqlField('screfid');

        $edit->addSQLConstraint(
            'Such 504 status already exists',
            "
            SELECT 1
              FROM webset.def_fifprocess
             WHERE screfid = ".$staterefid."
               AND (fipdesc = '[fipdesc]')
               AND fiprefid != AF_REFID
        ");

        $edit->finishURL = CoreUtils::getURL('process.php', array('staterefid'=>$staterefid));
        $edit->cancelURL = CoreUtils::getURL('process.php', array('staterefid'=>$staterefid));

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    } else {
        $list = new ListClass();
        $list->title = 'System 504 Process';

        $list->showSearchFields = true;

		$list->SQL = "
            SELECT fiprefid,
                   fipdesc,                   
                   CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
              FROM webset.def_fif_process
             WHERE screfid = ".$staterefid."
                   ADD_SEARCH
             ORDER BY fipdesc
        ";

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('504 Process');        
        $list->addColumn('Active Status')->type('switch')->sqlField('status');

        $list->addURL  = CoreUtils::getURL('process.php', array('staterefid'=>$staterefid));
        $list->editURL = CoreUtils::getURL('process.php', array('staterefid'=>$staterefid));
		
        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.def_fif_process')
                ->setKeyField('fiprefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
