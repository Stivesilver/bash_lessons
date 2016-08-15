<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT refid,
          	   s_src,
        	   s_date,
               report
          FROM webset_tx.std_speech_adata
       	 WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
       	 ORDER BY refid desc
        ";

	$list->title           = "Sources Of Data";
	$list->deleteTableName = "webset_tx.std_fie_social";
	$list->deleteKeyField  = "refid";
	$list->addURL          = CoreUtils::getURL('data_source_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('data_source_edit.php', array('dskey' => $dskey));

	$list->addColumn("Sources of Data");
	$list->addColumn("Date")
		->width('%')
		->type('date');

	$list->addColumn("Report Attached")->width('%');

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

	echo UIAnchor::factory('Professional Evaluator')
		->css('margin-left', '20px')
		->onClick('openEvaluator()')
		->toHTML();

	io::jsVar('dskey',      $dskey);
	io::jsVar('tsRefID',    $tsRefID);
	io::jsVar('stdIEPYear', $stdIEPYear);

?>

<script type="text/javascript">
	function openEvaluator() {
		var win = api.window.open(
			'Professional Evaluator',
			api.url(
				'evaluator_edit.php',
				{
					'tsRefID'   : tsRefID,
					'stdIEPYear': stdIEPYear
				}
			)
		);
	}
</script>