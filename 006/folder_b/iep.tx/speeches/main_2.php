<?php

	Security::init();

	$dskey   = io::get('dskey');
	$ds      = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID');
	$tabs    = new UITabs('tabs');

	$tabs->indent(5);

	$tabs->addTab(
		'Fluency',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/fluency_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 0       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Voice',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/voice_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 1       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Oral Peripheral',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/oral_peripheral_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 2       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Summary of Evaluation',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/summary_evaluation_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 3       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Recommendations',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/recommendations_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 4       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Signatures',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/signatures_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => '-1'    ,
				'top'     => 'yes'
			)
		)
	);

	echo $tabs->toHTML();

	print FFInput::factory()
		->name('screenURL')
		->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
		->hide()
		->toHTML();

?>