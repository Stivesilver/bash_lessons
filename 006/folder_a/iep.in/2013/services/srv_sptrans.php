<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Special Transportation';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '60%';

	$edit->setSourceTable('webset.std_in_special_transportation', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Is special transportation needed?', 'select')
		->name('sistsptransneededexcesstimequestion')
		->sqlField('sistsptransneededexcesstimequestion')
		->data(
			array(
				'N/A' => 'N/A',
				'Yes' => 'Yes',
				'No' => 'No'
			)
		)
		->emptyOption(TRUE);
	
	if ($set_ini['in_special_transportation_form_id'] > 0) {
		$form_state = IDEAFormTemplateXML::factory($set_ini['in_special_transportation_form_id']);
		$form_std = IDEAStudentFormXML::factory()
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($form_state->getFormId())
			->searchForm();

		$edit->addControl('Complete Form', 'protected')
			->append(
				UIAnchor::factory($form_state->getTitle() . ' ' . ($form_std->getFormId() > 0 ? 'Completed' : 'Not Completed'))
				->onClick('editForm("' . $form_state->getFormId() . '", "' . $form_std->getFormId() . '")')
			)->showIf('sistsptransneededexcesstimequestion', 'Yes');
	}

	$edit->addControl('If yes, is this excess transit time needed to meet the needs of the student as determined by the case conference committee?', 'select')
		->sqlField('sistsptransneededconfcommquestion')
		->data(
			array(
				'Yes' => 'Yes',
				'No' => 'No'
			)
		)
		->emptyOption(TRUE);
	
	$edit->addControl('If no special transportation is needed are there behaviors or other concerns (medical, etc.) that would effect transportation on a general education bus?', 'select')
		->name('busconcern')
		->sqlField('busconcern')
		->data(
			array(
				'N/A' => 'N/A',
				'Yes' => 'Yes',
				'No' => 'No'
			)
		)
		->emptyOption(TRUE);
	
	if ($set_ini['in_general_transportation_form_id'] > 0) {
		$form_state = IDEAFormTemplateXML::factory($set_ini['in_general_transportation_form_id']);
		$form_std = IDEAStudentFormXML::factory()
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($form_state->getFormId())
			->searchForm();

		$edit->addControl('Complete Form', 'protected')
			->append(
				UIAnchor::factory($form_state->getTitle() . ' ' . ($form_std->getFormId() > 0 ? 'Completed' : 'Not Completed'))
				->onClick('editForm("' . $form_state->getFormId() . '", "' . $form_std->getFormId() . '")')
			)->showIf('busconcern', 'Yes');
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('dskey', 'hidden')->value($dskey)->name('dskey');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_special_transportation')
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
			url = api.url('srv_sptrans_form_edit.ajax.php');
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