<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Edit Eligibility';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_eligibility', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl(
			FFSwitchYN::factory('Student is eligible for special education as defined under IDEA/Indiana  Article 7')
			->emptyOption(TRUE)
			->breakRow()
		)
		->sqlField('esw');

	$edit->addControl(
			FFSwitchYN::factory('Student should be referred to the building 504 coordinator')
			->emptyOption(TRUE)
			->breakRow()
		)
		->sqlField('e504sw');

	$edit->addControl($set_ini['in_eligibility_notes'], 'textarea')
		->sqlField('edesc')
		->css('width', '100%')
		->css('height', '150px');

	if ($set_ini['in_eligibility_evaluation_question'] != '') {

		$edit->addControl(
				FFSwitchYN::factory($set_ini['in_eligibility_evaluation_question'])
				->emptyOption(TRUE)
				->breakRow()
			)
			->name('reevaluation_sw')
			->sqlField('reevaluation_sw');

		if ($set_ini['in_eligibility_reevaluation_form_id'] > 0) {
			$form_state = IDEAFormTemplateXML::factory($set_ini['in_eligibility_reevaluation_form_id']);
			$form_std = IDEAStudentFormXML::factory()
				->setStdrefid($tsRefID)
				->setIepYear($stdIEPYear)
				->setStateFormId($form_state->getFormId())
				->searchForm();

			$edit->addControl('Complete Form', 'protected')
				->append(
					UIAnchor::factory($form_state->getTitle() . ' ' . ($form_std->getFormId() > 0 ? 'Completed' : 'Not Completed'))
						->onClick('editForm("' . $form_state->getFormId() . '", "' . $form_std->getFormId() . '")')
				)->showIf('reevaluation_sw', 'Y');
		}
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
			url = api.url('el_eligibility_form_edit.ajax.php');
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