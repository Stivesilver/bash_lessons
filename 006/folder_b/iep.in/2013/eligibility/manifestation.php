<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Edit Manifestation Determination';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_eligibility', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('A causal relationship between alleged misconduct and the student\'s disability exists.', 'select_radio')
		->data(
			array('Y' => 'Yes, a causal relationship between alleged misconduct and the student\'s disability exists ', 'N' => 'No, a causal relationship does not exist ', 'A' => 'N/A')
		)
		->breakRow()
		->name('casual_relation')
		->sqlField('casual_relation');

	$edit->addControl(
			FFSwitchYN::factory('Is the conduct in question caused by, or did it have a direct and substantial relationship to, the student\'s disability?')
			->emptyOption(TRUE)
			->breakRow()
		)
		->sqlField('direct_relation')
		->showIf('casual_relation', 'Y');

	$edit->addControl(
			FFSwitchYN::factory('Is the conduct a direct result of the School\'s failure to implement the student\'s IEP?')
			->emptyOption(TRUE)
			->breakRow()
		)
		->sqlField('school_failure')
		->showIf('casual_relation', 'Y');

	if ($set_ini['in_manifestation_determination_form_id'] > 0) {
		$form_state = IDEAFormTemplateXML::factory($set_ini['in_manifestation_determination_form_id']);
		$form_std = IDEAStudentFormXML::factory()
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($form_state->getFormId())
			->searchForm();

		$edit->addControl('Complete Form', 'protected')
			->append(
				UIAnchor::factory($form_state->getTitle() . ' ' . ($form_std->getFormId() > 0 ? 'Completed' : 'Not Completed'))
				->onClick('editForm("' . $form_state->getFormId() . '", "' . $form_std->getFormId() . '")')
			)->showIf('casual_relation', 'N');
	}


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('dskey', 'hidden')->value($dskey)->name('dskey');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_eligibility')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
<script type="text/javascript">

		function editForm(state_id, std_id) {
			url = api.url('manifestation_form_edit.ajax.php');
			api.ajax.post(
				url,
				{
					'state_id': state_id,
					'std_id': std_id,
					'dskey': $('#dskey').val()
				},
			function(answer) {
				win = api.window.open(answer.caption, answer.url);
				win.maximize();
				win.addEventListener(WindowEvent.CLOSE, formCompleted);
				win.show();
			}
			);
		}

		function formCompleted() {
			var edit1 = EditClass.get();
			edit1.saveAndEdit()
		}
</script>