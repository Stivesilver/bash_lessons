<?php
    Security::init();

    $RefID = io::get('RefID');
    $staterefid = io::geti('staterefid');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit System Current Student 504 Status';

        $edit->setSourceTable('webset.def_fif_status', 'fifrefid');

        $edit->addGroup('General Information');

        $edit->addControl('504 Status')->sqlField('fifdesc')->name('fifdesc')->size(80)->req();
        $edit->addControl(FFSwitchYN::factory('504 Active'))->sqlField('active_sw')->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('State ID', 'hidden')->value($staterefid)->sqlField('screfid');

        $edit->addSQLConstraint(
            'Such 504 status already exists',
            "
            SELECT 1
              FROM webset.def_fif_status
             WHERE screfid = ".$staterefid."
               AND (fifdesc = '[fifdesc]')
               AND fifrefid != AF_REFID
        ");

        $edit->finishURL = CoreUtils::getURL('status.php', array('staterefid'=>$staterefid));
        $edit->cancelURL = CoreUtils::getURL('status.php', array('staterefid'=>$staterefid));

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    } else {
        $list = new ListClass();
        $list->title = 'System Current Student 504 Status';

        $list->showSearchFields = true;

		$list->SQL = "
            SELECT fifrefid,
                   fifdesc,
                   active_sw,
                   CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
              FROM webset.def_fif_status
             WHERE screfid = ".$staterefid."
                   ADD_SEARCH
             ORDER BY fifdesc
        ";

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('504 Status');
        $list->addColumn('504 Active')->type('switch')->sqlField('active_sw');
        $list->addColumn('Active Status')->type('switch')->sqlField('status');
		
        $list->addURL  = CoreUtils::getURL('status.php', array('staterefid'=>$staterefid));
        $list->editURL = CoreUtils::getURL('status.php', array('staterefid'=>$staterefid));
		
        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.def_fif_status')
                ->setKeyField('fifrefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>