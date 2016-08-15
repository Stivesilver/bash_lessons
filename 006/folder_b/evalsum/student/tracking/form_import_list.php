<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$stdrefid = $ds->safeGet('stdrefid');
	$set_ini = IDEAFormat::getIniOptions();

	$list = new ListClass();

	$list->title = $set_ini['iep_title'] . ' Documentation';

	$list->SQL = "
		SELECT sfrefid,
			   form_name,
			   siymiepbegdate,
			   siymiependdate,
			   std.lastupdate,
			   std.lastuser,
			   archived,
			   stt.frefid
		  FROM webset.std_forms_xml std
			   INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.tsrefid
			   INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
			   INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.mfcprefid
			   LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = std.iepyear
		 WHERE ts.stdrefid = " . $stdrefid . " 
		 ORDER BY sfrefid DESC
    ";

	$list->addSearchField('Form Title', 'form_name')->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('Title')
		->sqlField('form_name');
	$list->addColumn($set_ini['iep_year_title'])
		->dataCallback(
			create_function(
				'$data',
				'return CoreUtils::formatDateForUser($data["siymiepbegdate"]) . " - " . CoreUtils::formatDateForUser($data["siymiependdate"]);'
			)
		);
	$list->addColumn('Completed On')
		->type('date')
		->sqlField('lastupdate');
	$list->addColumn('Completed By')
		->sqlField('lastuser');
	$list->addColumn('Archived')
		->type('switch')
		->sqlField('archived');

	$list->multipleEdit = false;

	$list->addRecordsProcess('Import')
		->url(CoreUtils::getURL('form_import.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->onProcessDone('importDone')
		->progressBar(false);

	$list->printList();

?>
<script>
	function importDone() {
		api.window.dispatchEvent('forms_imported');
		api.window.destroy();
	} 
</script>
