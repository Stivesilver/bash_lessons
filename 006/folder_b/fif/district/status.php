<?php

	Security::init();

	$RefID = io::get('RefID');

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit 504 Status';

		$edit->setSourceTable('webset.disdef_fif_status', 'difrefid');

		$edit->addGroup('General Information');
		$edit->addControl('District Current Student 504 Status')->sqlField('difdesc')->name('difdesc')->size(80)->req();
		$edit->addControl('System Current Student 504 Status', 'list')
			->sqlField('statecode_id')
			->sql("
                SELECT fifrefid,
	                   fifdesc
	              FROM webset.def_fif_status
	             WHERE screfid = " . VNDState::factory()->id . "
	               AND (enddate IS NULL or now()< enddate)
	             ORDER BY fifdesc
            ")
			->emptyOption(true)
			->req();

		$edit->addControl('Deactivation Date', 'date')->sqlField('enddate')->name('enddate');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		$edit->addSQLConstraint(
			'Such status already exists', "
            SELECT 1
              FROM webset.disdef_fif_status
             WHERE vndrefid = VNDREFID
               AND difdesc = '[difdesc]'
               AND difrefid != AF_REFID
        ");

		$edit->finishURL = 'status.php';
		$edit->cancelURL = 'status.php';

		$edit->firstCellWidth = '30%';

		$edit->printEdit();
	} else {
		if(file_exists(SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_504.txt')) {
			unlink(SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_504.txt');
		}
		$list = new ListClass();

		$list->title = 'District Current Student 504 Status';
		$list->showSearchFields = true;

		$list->SQL = "
            SELECT difrefid,
                   difdesc,
                   fifdesc,
                   CASE WHEN NOW() > district.enddate THEN 'N' ELSE 'Y' END as status
              FROM webset.disdef_fif_status district
                   INNER JOIN webset.def_fif_status state ON state.fifrefid = district.statecode_id
             WHERE vndrefid = VNDREFID
               AND (state.enddate IS NULL OR NOW() < state.enddate)
                   ADD_SEARCH
             ORDER BY difdesc
        ";

		$list->addSearchField(
			FFSwitchAI::factory('Status')
				->sqlField("CASE WHEN NOW() > district.enddate THEN 'I' ELSE 'A' END")
				->value('A')
		);

		$list->addColumn('District Current Student 504 Status');
		$list->addColumn('System Current Student 504 Status');
		$list->addColumn('Active')->type('switch')->sqlField('status');

		$list->addURL = 'status.php';
		$list->editURL = 'status.php';


		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_fif_status')
				->setKeyField('difrefid')
				->applyListClassMode()
		);

		$list->printList();
	}
?>
