<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsRefID');

	$edit->title = 'Early Childhood Question';

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->saveAndAdd = FALSE;
	$edit->saveAndEdit = TRUE;
	$edit->addGroup('General Information');
	$edit->addControl(FFSwitchYN::factory('Early Childhood Student'))->value('N')->sqlField('stdearlychildhoodsw');
	$edit->addControl(FFSwitchYN::factory('Is this child a referral from First Steps?'))->sqlField('bipsw')->name('bipsw');
	$edit->addControl('dskey', 'hidden')->value($dskey)->name('dskey');

	
	if ($set_ini['in_ec_form_id'] > 0) {
		$form_state = IDEAFormTemplateXML::factory($set_ini['in_ec_form_id']);
		$form_std = IDEAStudentFormXML::factory()
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($form_state->getFormId())
			->searchForm();

		$edit->addControl('Complete Form', 'protected')
			->append(
				UIAnchor::factory($form_state->getTitle() . ' ' . ($form_std->getFormId() > 0 ? 'Completed' : 'Not Completed'))
				->onClick('editForm("' . $form_state->getFormId() . '", "' . $form_std->getFormId() . '")')
			)->showIf('bipsw', 'Y');
	}


	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyEditClassMode()
	);

	$edit->printEdit();
?>
<script type="text/javascript">

		function editForm(state_id, std_id) {
			url = api.url('ec_question_form_edit.ajax.php');
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