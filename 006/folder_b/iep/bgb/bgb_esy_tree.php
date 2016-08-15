<?PHP
	Security::init();

	$dskey = io::get('dskey');
	io::jsVar('dskey', $dskey);

	io::css('.mpSelected', 'background-color: #BBB !important; color: #000 !important; border: 1px solid #888');
	io::css('.trItem', 'border-radius: 5px; margin: 1px; padding: 0px 10px 0px 10px');

	$tw = new UITreeWrapper(
		CoreUtils::getURL('./bgb_esy_tree.ajax.php', array('dskey' => $dskey)),
		'tw'
	);

	$tw->collapseRightFrame(true);

	$tree = $tw->getTree()
		->flagMode(true)
		->defaultIcon('')
		->selectionClassName('mpSelected');

	$tw->toolButtons(true);

	$tw->className('zBox9 zLightLines');

	echo UIFrameSet::factory('100%', '*40, auto, *40')
		->addFrame(
			UIFrame::factory()
				->className('zBox2')
				->addObject(
					UILayout::factory()->addHTML('ESY Entry Creation', 'center .zPageTitle [padding: 5px]')
				)
		)
		->addFrame(UIFrame::factory()->addObject($tw))
		->addFrame(
			UIFrame::factory()
				->className('zBox4')
				->addObject(
					UILayout::factory()
						->addObject(
							FFButton::factory('Cancel')
								->onClick('cancel();')
								->width(160)
							, 'left [padding: 5px]')
						->addObject(
							FFButton::factory('Copy Goals')
								->onClick('copyGoal();')
								->id('copygoal')
								->width(160)
							, 'right [padding: 5px]')
				)
		)
		->toHTML();

?>

<script>
	function copyGoal() {
		$("#copygoal").attr("disabled","disabled");
		/** @type {Array.<UITreeItem>} list */
		var list = UITree.get().getNestedFlaggedItems();
		var baselines = new Object();
		var goals = new Object();
		var benchmarks = new Object();
		for (var i = 0; i < list.length; i++) {
			if (list[i].category() == 'bs') {
				baselines[i] = list[i].value();
			}
			if (list[i].category() == 'gl') {
				goals[i] = new Object();
				goals[i].id = list[i].value();
				goals[i].parent = list[i].parentItem().value();
			}
			if (list[i].category() == 'bn') {
				benchmarks[i] = new Object();
				benchmarks[i].id = list[i].value();
				benchmarks[i].parent = list[i].parentItem().value();
			}
		}
		api.ajax.post(
			'./bgb_esy.ajax.php',
			{
				'baselines': JSON.stringify(baselines),
				'goals': JSON.stringify(goals),
				'benchmarks': JSON.stringify(benchmarks),
			    'dskey' : JSON.stringify(dskey)
			},
			function (answer) {
				if (answer.res == 1) {
					api.reload();
				}
			}
		);
	}

	function cancel() {
		window.location = "<?=$g_virtualRoot;?>/applications/webset/iep/wrk_stdmgr_menu_back.php";
	}
</script>
