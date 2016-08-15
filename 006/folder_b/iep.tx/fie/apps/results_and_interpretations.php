<?php

	Security::init();

	$dskey      = io::get('dskey');
	$keyGroup   = io::get('key_group');
	$keyName    = io::get('key_name');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', 0);

	$edit->title       = 'Results and Interpretations';
	$edit->saveAndAdd  = false;

	$edit->addGroup('General Information');
	$edit->addControl('-Results and Interpretations', 'textarea')
		->name('text')
		->width('100%')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, $keyGroup, $keyName, $stdIEPYear));

	$edit->addUpdateInformation();

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_registry')
			->setKeyField('rrefid')
			->applyEditClassMode()
			->setRefids(IDEAStudentRegistry::getRecordID($tsRefID, $keyGroup, $keyName, $stdIEPYear))
	);

	$edit->addButton(
		FFButton::factory('Save & Finish')
			->onClick('saveAndSwitch(' . io::get('lasttab') . ')')
			->css('width: 115px;')
	);

	$edit->addButton(
		FFButton::factory('Save & Edit')
			->onClick('saveText(true)')
			->css('width: 115px;')
	);

	$edit->printEdit();

	io::jsVar('tsRefID',    $tsRefID);
	io::jsVar('keyGroup',   $keyGroup);
	io::jsVar('keyName',    $keyName);
	io::jsVar('stdIEPYear', $stdIEPYear);

?>

<script type="text/javascript">
	function saveAndSwitch(lasttab) {
		saveText(false);
		if (lasttab == 0) {
			parent.switchTab();
		} else {
			parent.parent.selectNext();
		}
	}

	 /*save text from textarea*/
	function saveText(open) {
		var text = $('#text').val();
		api.ajax.post(
			'sources_save_text.ajax.php',
			{
				'text'      : text,
				'tsRefID'   : tsRefID,
				'keyName'   : keyName,
				'keyGroup'  : keyGroup,
				'stdIEPYear': stdIEPYear
			},
			function(answer) {
				/* if we switch tab or app not displayed alert */
				if (answer.res == 1 && open == true) {
					api.reload();
				}
			}
		);
	}
</script>