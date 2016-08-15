<?

    Security::init();

    $dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

    $tabs = new UITabs('tabs');
    $tabs->indent(5);
	
	$tabs->addTab(
		'1. Assessment Summary', CoreUtils::getURL('assessment.php', array('dskey' => $dskey))
	);

	$tabs->addTab(
		'2. Present Level', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', 
			array(
				'dskey' => $dskey,
				'constr' => '129',
				'top' => 'no',
				'nexttab' => '2'
				
			)
		)
	);
	
	$tabs->addTab(
		'3. Student Input', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', 
			array(
				'dskey' => $dskey,
				'constr' => '130',
				'top' => 'no',
				'nexttab' => '3'
				
			)
		)
	);
	
	$tabs->addTab(
		'4. Postsecondary Goals', CoreUtils::getURL('postgoals.php', array('dskey' => $dskey))
	);
	
	$tabs->addTab(
		'5.	Skill Areas', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', 
			array(
				'dskey' => $dskey,
				'constr' => '131',
				'top' => 'no',
				'desktop' => 'yes'
				
			)
		)
	);
	
    print $tabs->toHTML();
	
	print FFInput::factory()
			->name('screenURL')
			->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
			->hide()
			->toHTML();
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