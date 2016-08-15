<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT refid,
               test,
               score,
               rank
          FROM webset_tx.std_speech_lang_scores
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
         ORDER BY refid desc
        ";

	$list->title = "Language Assessment";

	$list->addColumn("Test");

	$list->addColumn("Standard Score")->width('%');
	$list->addColumn("Percentile Rank")->width('%');

	$list->addURL          = CoreUtils::getURL('language_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('language_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_speech_lang_scores";
	$list->deleteKeyField  = "refid";

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

	echo UIAnchor::factory('Formal Assessment')
		->css('margin-left', '20px')
		->onClick('openAssessment()')
		->toHTML();

	io::jsVar('dskey',      $dskey);
	io::jsVar('tsRefID',    $tsRefID);
	io::jsVar('stdIEPYear', $stdIEPYear);

?>

<script type="text/javascript">
	function openAssessment() {
		var win = api.window.open(
			'Formal Assessment',
			api.url(
				'assessment_edit.php',
				{
					'tsRefID'   : tsRefID,
					'stdIEPYear': stdIEPYear
				}
			)
		);
	}
</script>