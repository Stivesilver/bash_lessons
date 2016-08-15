<?php

	Security::init();

	$dskey = io::get('dskey');
	$area = io::get('area');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Related Services Dates';

	$edit->setSourceTable('webset.std_additionalinfo', 'siairefid');

	$edit->addGroup('General Information');
	$edit->addControl('Text', 'textarea')
		->sqlField('siaitext')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl('Area', 'hidden')->value($area)->sqlField('docarea');

	$edit->finishURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey, 'area' => $area));
	$edit->cancelURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey, 'area' => $area));

	$edit->printEdit();
?>

<script>
	EditClass.get().addEventListener(
		ObjectEvent.CANCEL,
		checkClose
	);

	api.window.addEventListener(
		WindowEvent.CLOSE,
		checkClose
	);

	function checkClose(e) {
		if (confirm('Unsaved data might be lost if you close the window. To save data click on the "Save & Finish" button, if you do not want to save click the "Cancel" button.\nAre you sure you want to close the window?')) {
			return true;
		} else {
			e.preventDefault();
		}
	}

</script>
