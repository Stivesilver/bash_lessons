<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$text = "1) How will outside circumstances/antecedents be removed, modified or accommodated?\n2) Acceptable replacement behaviors to be taught that decrease/eliminate inappropriate behavior.\n3) Persons that will be responsible for teaching replacement behaviors.";
	$text = trim($text);

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Edit Behavior Intervention Plan (General Part)';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset.std_in_bipgen', 'stdrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Plan of Action', 'textarea')
		->sqlField('planoa')
		->css('width', '100%')
		->css('height', '150px')
		->name('action_plan')
		->value($text);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = 'javascript:parent.switchTab(3);';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(FFButton::factory('Default'))
		->width(80)
		->onClick('defText(' . json_encode($text) . ');');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_bipgen')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>

<script>
	function defText(text) {
		$('#action_plan').val(text);
	}
</script>
