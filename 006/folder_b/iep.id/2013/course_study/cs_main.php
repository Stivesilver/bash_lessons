<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);

	$tabs->addTab(
		'1. High School', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '135',
			'top' => 'no',
			'nexttab' => '1'
			)
		)
	);

	$tabs->addTab(
		'2. Courses of Study', CoreUtils::getURL('cs_courses.php', array('dskey' => $dskey))
	);

	$tabs->addTab(
		'3. College Entrance Exam', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '137',
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