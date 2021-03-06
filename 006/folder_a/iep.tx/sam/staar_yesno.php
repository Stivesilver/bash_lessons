<?php
	Security::init();


	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $samrefid);

	$edit->title = $assess;
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset_tx.std_sam_general', 'samrefid');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('The student will take the ' . $assess)
			->sqlField(strtolower($assess) . '_take')
			->name('take')
			->onChange('var edit1 = EditClass.get(); edit1.saveAndEdit();')
			->data(array('Y' => 'Yes', 'N' => 'No', 'A' => 'N/A'))
	);

	$edit->addControl('If no, identify the reason', 'textarea')
		->sqlField(strtolower($assess) . '_whynot')
		->css('width', '100%')
		->css('height', '80px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = 'javascript:api.window.destroy()';
	$edit->cancelURL = 'javascript:api.window.destroy()';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_general')
			->setKeyField('samrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
<script type="text/javascript">

		var edit1 = EditClass.get();
		edit1.onSaveDoneFunc(
			function(refid) {
				parent.adjustTabs($('#take').val());
			}
		)
		edit1.onSaveDoneFunc();
</script>
