<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area_id = 113;

	$list = new ListClass();

	$list->title = 'Data Entry Forms';

	$list->SQL = "
		SELECT refid,
			   lastuser,
			   lastupdate,
			   'Print'
		  FROM webset_tx.std_dataentry
		 WHERE stdrefid = " . $tsRefID . " 
		   AND iepyear = " . $stdIEPYear . "	
		 ORDER BY lastupdate desc
	";

	$list->addColumn('Archived By');
	$list->addColumn('Archived On')->type('date');
	$list->addColumn('Print')
		->type('link')
        ->param('javascript:buildDoc(AF_REFID);');

	$list->addURL = CoreUtils::getURL('main_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('main_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_dataentry';
	$list->deleteKeyField = 'refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);
	
	print FFInput::factory('Gen File')	
        ->name('gen_file')
		->value(SystemCore::$virtualRoot . '/applications/webset/iep.tx/dataentry/buildPDF.php')
		->hide()
		->toHTML();

	$list->printList();
?>
<script type="text/javascript">
    function buildDoc(id) {
        url = api.url('main_build.ajax.php', {'id': id});
        win = api.ajax.process(ProcessType.REPORT, url);
    }
</script>