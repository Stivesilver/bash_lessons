<?

    Security::init();

    $dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

    $tabs = new UITabs('tabs');
    $tabs->indent(5);

	$tabs->addTab(
		'1. Postsecondary Goals',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey' => $dskey,
				'constr' => '165',
				'top' => 'no',
				'nexttab' => '1'

			)
		)
	);

	$tabs->addTab(
		'2. Transition Services',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey' => $dskey,
				'constr' => '166',
				'top' => 'no',
				'nexttab' => '2'

			)
		)
	);

	$tabs->addTab(
		'3. Courses of Study',
		CoreUtils::getURL(
			'cs_courses.php',
			array(
				'dskey' => $dskey 
			)
		)
	);

	$tabs->addTab(
		'4. Transfer of rights',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey' => $dskey,
				'constr' => '168',
				'top' => 'no',
				'nexttab' => '4'

			)
		)
	);

	$tabs->addTab(
		'5. Graduation',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey' => $dskey,
				'constr' => '169',
				'top' => 'no',
				'nexttab' => '5'

			)
		)
	);

	$tabs->addTab(
		'6. Student Participation',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey' => $dskey,
				'constr' => '170',
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
