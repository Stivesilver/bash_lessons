<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$pdf_list = db::execSQL("
		SELECT purp.mfcpdesc,
		       forms.mfcdoctitle,
		       forms.mfcfilename
		  FROM webset.statedef_forms forms
		       INNER JOIN webset.def_formpurpose purp ON forms.mfcprefid = purp.mfcprefid
		 WHERE forms.mfcprefid IN (" . IDEAFormat::getIniOptions('procedural_safeguards_id') . ")
		   AND forms.screfid = " . VNDState::factory()->id . "
		   AND (
				   forms.recdeactivationdt IS NULL
				OR now()< forms.recdeactivationdt
		       )
		 ORDER BY purp.mfcpdesc, forms.mfcdoctitle
	")->assocAll();

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Other Forms';
	$edit->firstCellWidth = '30%';

	$cur_title = ''; 
	foreach ($pdf_list AS $item) {
		if ($cur_title != $item['mfcpdesc']) {
			$cur_title = $item['mfcpdesc'];
			$edit->addGroup($cur_title);
		} 
		$edit->addControl($item['mfcdoctitle'], 'protected')
			->append(
				SystemCore::$FS->exists(SystemCore::$physicalRoot . '/applications/webset/iep/evalforms/docs/' . $item['mfcfilename']) ?
					UIAnchor::factory('Open Form')->onClick('openForm("' . SystemCore::$physicalRoot . '/applications/webset/iep/evalforms/docs/' . $item['mfcfilename'] . '")')->toHTML() :
					UIMessage::factory('Procedural Safeguards Form has not yet set up.')
			);
	}

	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->printEdit();
?>
<script type="text/javascript">

	function openForm(file) {
		url = api.url('form_edit.ajax.php');
		api.ajax.process(ProcessType.REPORT, url, {'file': file});
	}

	function formCompleted() {
		api.reload();
	}
</script>
