<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');
	
	$tabs->indent(3);
	
	$tabs->addTab(
		'Eligibility', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'constr' => '199',
				'dskey' => $dskey,
				'nexttab' => 1,
				'help' => '179',
				'top' => 'no'
			)
		)
	);

	$tabs->addTab(
		'IFSP Team Members', 
		CoreUtils::getURL(
			'/apps/idea/iep.tn/general/participant_list.php', 
			array(
					'dskey'   => $dskey,
					'nexttab' => 2
			)
		)
	);
	
	$tabs->addTab(
		'Service Coordinator/Agency', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'constr' => '195',
				'dskey' => $dskey,
				'nexttab' => 3,
				'help' => '179',
				'top' => 'no'
			)
		)
	);

	$tabs->addTab(
		'Informed Parental Consent', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'constr' => '197',
				'dskey' => $dskey,
				'help' => '179'
			)
		)
	);

	echo $tabs->toHTML();
	echo FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();
   	 
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
