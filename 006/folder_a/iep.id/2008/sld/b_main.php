<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey, true);
	$tabs = new UITabs('tabs');

	$tabs->indent(9);

	$tabs->addTab(
		'1. Strenghts',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '92',
				'iep' => 'no',
				'nexttab' => 1,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'2. Health',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '93',
				'iep' => 'no',
				'nexttab' => 2,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'3. Notification',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '94',
				'iep' => 'no',
				'nexttab' => 3,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'4. Curriculum',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/b_curriculum_list.php',
			array(
				'dskey' => $dskey,
			)
		)
	);

	$tabs->addTab(
		'5a. Instructions',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/b_instructions_list.php',
			array(
				'dskey' => $dskey,
				'nexttab' => 5,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'5b. Interventions',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/b_intervention_list.php',
			array(
				'dskey' => $dskey,
				'nexttab' => 6,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'5c. Difficulty',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '95',
				'iep' => 'no',
				'nexttab' => 7,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'6. Summary',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '96',
				'iep' => 'no',
				'nexttab' => 8,
				'top' => 'yes'
			)
		)
	);

	$tabs->addTab(
		'7. Observation',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey' => $dskey,
				'constr' => '97',
				'iep'  => 'no',
				'top' => 'yes',
				'desktop' => 'yes'
			)
		)
	);

	print $tabs->toHTML();
	print FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();

?>

<script type="text/javascript">
	function switchTab(id) {
		var tab1 = UITabs.get('tabs');
		if (id >= 0) {
			tab1.switchTab(id);
		} else {
			api.goto($('#screenURL').val());
		}
	}
</script>