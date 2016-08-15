<?php

	Security::init();

	$RefID = io::geti('RefID');
	$stdrefid = io::geti('stdrefid');

	#calculate id medicaid
	$id = (int) db::execSQL("
		SELECT msm_refid
		  FROM webset.med_std_main
		 WHERE stdrefid = $stdrefid
	")->getOne();

	$sql = "
		SELECT stdfedidnmbr
		  FROM webset.dmg_studentmst
		 WHERE stdRefID = " . $stdrefid;
	$federalIdNumber = db::execSQL($sql)
		->getOne();

	$edit = new EditClass('edit1', $id);
	$student   = Student::factory($stdrefid);
	$guardians = Student::factory($stdrefid)->getGuardians();

	$edit->title = 'Student Medicaid Processing';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;
	$edit->finishURL = 'javascript:api.window.destroy();';
	$edit->cancelURL = 'javascript:api.window.destroy()';
	$edit->firstCellWidth = '40%';

	$edit->setSourceTable('webset.med_std_main', 'msm_refid');

	$edit->addGroup('General Information');

	$edit->addControl('Student Name', 'protected')
		->name('stdname')
		->value($student->getName());

	$edit->addControl('Current Grade Level', 'protected')
		->value($student->getGrade());

	$edit->addGroup('Parent Information');
	foreach ($guardians as $guardian) {
		/** @var $guardian Guardian */
		$edit->addControl($guardian->getRelationType(), 'protected')
			->value($guardian->getName());
	}

	$edit->addGroup('Medicaid Information');
	$edit->addControl('Student Medicaid #', 'text')
		->sqlField('msm_medicaid');

	$edit->addControl('Federal ID', 'protected')
		->value($federalIdNumber);

	$edit->addControl(FFSwitchYN::factory('Medicaid Eligible'))
		->emptyOption(false)
		->value('Y')
		->name('msm_medicaid_eligible_sw')
		->sqlField('msm_medicaid_eligible_sw');

	$edit->addControl(FFButton::factory('Work With Forms'))
		->showIf('msm_medicaid_eligible_sw', 'Y')
		->onClick('openFormsList(' . $id . ')');

	$edit->addControl(FFSwitchYN::factory('Consent from Parent \ Guardian to Bill Medicaid'))
		->emptyOption(false)
		->value('N')
		->sqlField('msm_consent_from_parent_sw');

	$edit->addControl(FFSwitchYN::factory('Consent \ Revoke Form(s) Received from Parent \ Guardian to Bill Medicaid'))
		->emptyOption(false)
		->value('N')
		->sqlField('msm_consent_sw');

	$edit->addControl('stdrefid', 'hidden')
		->name('stdrefid')
		->sqlField('stdrefid')
		->value($stdrefid);

	$edit->addUpdateInformation();

	$edit->setPresaveCallback('updateDemograph', 'update_demograph.inc.php');

	$edit->printEdit();
?>
<script type="text/javascript">
	api.window.changeTitle($('#stdname').val() + ', Lumen ID: ' +$('#stdrefid').val());

	function openFormsList(id) {
		url = api.url(
			'forms_list.php',
			{'msm_refid': id}
		);
		win = api.window.open(
			'Work With Consent Form(s) Received from Parent / Guardian to Bill Medicaid',
			url
		);
	}
</script>