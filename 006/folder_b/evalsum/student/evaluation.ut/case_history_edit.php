<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $evalproc_id);

	$edit->title = 'Case History';
	$edit->topButtons = true;

	$edit->setSourceTable('webset.es_std_er_casehistory', 'eprefid');

	$edit->addGroup('General Information');

	$edit->addControl('Description of Educational Concerns', 'textarea')
		->css('height', '120px')
		->sqlField('concerns');

	$edit->addControl('Intervention Strategies Used Prior to Referral', 'textarea')
		->css('height', '120px')
		->sqlField('interventions');

	$edit->addControl('School History', 'textarea')
		->css('height', '120px')
		->help('include previous school(s) attended, grades retained, attendance, previous services,  Title I services, current classroom performance')
		->sqlField('school_history');

	$edit->addControl('Family History', 'textarea')
		->css('height', '120px')
		->help('include developmental milestones, parent concerns, and relevant medical history')
		->sqlField('family_history');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_er_casehistory')
			->setKeyField('eprefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
