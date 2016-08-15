<?php
    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit 504 Process';

        $edit->setSourceTable('webset.disdef_fif_process', 'diprefid');

        $edit->addGroup('General Information');
        $edit->addControl('District 504 Process')->sqlField('dipdesc')->name('dipdesc')->size(80)->req();
        $edit->addControl('System 504 Process', 'list')
            ->sqlField('statecode_id')
            ->sql("
                SELECT fiprefid,
	                   fipdesc
	              FROM webset.def_fif_process
	             WHERE screfid = ".VNDState::factory()->id."
	               AND (enddate IS NULL or now()< enddate)
	             ORDER BY fipdesc
            ")
            ->emptyOption(true)
            ->req();

        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate')->name('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint(
            'Such status already exists',
            "
            SELECT 1
              FROM webset.disdef_fif_process
             WHERE vndrefid = VNDREFID
               AND dipdesc = '[dipdesc]'
               AND diprefid != AF_REFID
        ");

        $edit->finishURL = 'process.php';
        $edit->cancelURL = 'process.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    } else {
	    if(file_exists(SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_504.txt')) {
		    unlink(SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_504.txt');
	    }
        $list = new ListClass();

        $list->title = 'District 504 Process';
        $list->showSearchFields = true;

        $list->SQL = "
            SELECT diprefid,
                   dipdesc,
                   fipdesc,
                   CASE WHEN NOW() > district.enddate THEN 'N' ELSE 'Y' END as status
              FROM webset.disdef_fif_process district
                   INNER JOIN webset.def_fif_process state ON state.fiprefid = district.statecode_id
             WHERE vndrefid = VNDREFID
               AND (state.enddate IS NULL OR NOW() < state.enddate)
                   ADD_SEARCH
             ORDER BY dipdesc
        ";

		$list->addSearchField(
			FFSwitchAI::factory('Status')
				->sqlField("CASE WHEN NOW() > district.enddate THEN 'I' ELSE 'A' END")
				->value('A')
		);

        $list->addColumn('District 504 Process');
        $list->addColumn('System 504 Process');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL  = 'process.php';
        $list->editURL = 'process.php';

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_fif_process')
				->setKeyField('diprefid')
				->applyListClassMode()
		);

        $list->printList();
    }
?>
