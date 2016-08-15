<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdrefid   = $ds->safeGet('stdrefid');
	$list       = new ListClass();

	$list->SQL = "
		SELECT sIEPMRefID,
               sIEPMDocDate,
               COALESCE(rptype, 'Full And Individual Evaluation (FIE)'),
               to_char(iep.lastupdate,'MM-DD-YYYY') as sIEPMDocDate,
               iep.lastuser
		  FROM webset_tx.std_fie_arc iep
               INNER JOIN webset.sys_teacherstudentassignment ts ON iep.stdrefid = ts.tsrefid
		 WHERE (iep_status!='I' or iep_status is Null)
           AND ts.stdrefid = $stdrefid
         ORDER BY iep.lastupdate desc
        ";

	$list->title        = "FIE Builder";
	$list->multipleEdit = true;

	$list->addColumn("FIE Meeting Date")
		->width('10%')
		->type('date');

	$list->addColumn("Type of ARD")->width('60%');
	$list->addColumn("Archive Date")->width('10%');
	$list->addColumn("Archived By")->width('10%');
	$list->addColumn("Doc")
		->width('10%')
		->dataCallback('linkToDoc');

	$list->addURL       = CoreUtils::getURL("pdf_builder_edit.php", array('dskey' => $dskey));
	$list->editURL      = "javascript:api.ajax.process(UIProcessBoxType.REPORT, api.url('" . CoreUtils::getURL('get_file.ajax.php') . "', {'RefID' : AF_REFID}))";

	$list->addButton('Upload')
		->onClick('uploadForm();');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_fie_arc')
			->setKeyField('siepmrefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Disable')
		->message('Do you really want to delete this IEP?')
		->url(CoreUtils::getURL('builder_disable.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

	function linkToDoc() {
		return UIAnchor::factory('PDF')
			->onClick('getFile()')
			->toHTML();
	}

	io::jsVar('stdrefid', $tsRefID);
	io::jsVar('dskey',    $dskey);

?>

<script type="text/javascript">
	function getFile() {
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('get_file.ajax.php'),
			{}
		);
	}

	function uploadForm() {
		var url = api.url('forms_add.php',
			{'stdrefid': stdrefid}
		);
		var win = api.goto(url, {'dskey': dskey});
	}

</script>