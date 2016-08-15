<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$ini = IDEAFormat::getIniOptions();
	$file = CoreUtils::getPhysicalPath($ini['procedural_safeguards_form_file']);
	$file_spanish = CoreUtils::getPhysicalPath($ini['procedural_safeguards_spanish_form_file']);

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Procedural Safeguards';
	$edit->firstCellWidth = '30%';

	$edit->addGroup('General Information');
	$edit->addControl('Procedural Safeguards Form', 'protected')
		->append(
			file_exists($file) ?
				UIAnchor::factory('Open Form')->onClick('openForm("' . $ini['procedural_safeguards_form_file'] . '")')->toHTML() :
				UIMessage::factory('Procedural Safeguards Form has not yet set up.')
	);

	if (file_exists($file_spanish)) {

		$edit->addControl('Procedural Safeguards Spanish Form', 'protected')
			->append(
				file_exists($file_spanish) ?
					UIAnchor::factory('Open Form')->onClick('openForm("' . $ini['procedural_safeguards_spanish_form_file'] . '")')->toHTML() :
					UIMessage::factory('Procedural Safeguards Spanish Form has not yet set up.')
		);
	}

	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->printEdit();
?>
<script type="text/javascript">

		function openForm(file) {
			url = api.url('safeguards.ajax.php');
			api.ajax.process(ProcessType.REPORT, url, {'file': file});
		}

		function formCompleted() {
			api.reload();
		}
</script>
