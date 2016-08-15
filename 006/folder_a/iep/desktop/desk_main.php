<?php

	Security::init();

	function checkBlock($tsRefID, $stdIEPYear, $path) {
		$fullpath = SystemCore::$physicalRoot . '/apps/idea' . $path;
		if (file_exists($fullpath) && $path && $tsRefID > 0 && $stdIEPYear > 0) {
			$a = include($fullpath);
			return $a;
		}
		return true;
	}

	$tsRefID = CryptClass::factory()->decode(io::get('tsRefID'));
	$student = new IDEAStudent($tsRefID);
	$dskey = DataStorage::factory()
		->set('tsRefID', $tsRefID)
		->set('stdIEPYear', $student->get('stdiepyear'))
		->set('stdrefid', $student->get('stdrefid'))
		->set('stdname', $student->get('stdname'))
		->set('stdnamefml', $student->get('stdnamefml'))
		->set('stdlastname', $student->get('stdlastname'))
		->set('stdfirstname', $student->get('stdfirstname'))
		->set('stddob', $student->get('stddob'))
		->set('stdsex', $student->get('stdsex'))
		->set('stdschid', $student->get('stdschid'))
		->set('stdfedidnmbr', $student->get('stdfedidnmbr'))
		->set('stdstateidnmbr', $student->get('stdstateidnmbr'))
		->set('externalid', $student->get('externalid'))
		->set('grdlevel', $student->get('grdlevel'))
		->set('grdlevel_id', $student->get('grdlevel_id'))
		->set('ethcode', $student->get('ethcode'))
		->set('ethdesc', $student->get('ethdesc'))
		->set('vourefid', $student->get('vourefid'))
		->set('schoolid', $student->get('schoolid'))
		->set('stdage', $student->get('stdage'))
		->set('prim_lang', $student->get('prim_lang'))
		->set('stdhphn', $student->get('stdhphn'))
		->set('stdhphnmob', $student->get('stdhphnmob'))
		->set('stdhadr1', $student->get('stdhadr1'))
		->set('stdhcity', $student->get('stdhcity'))
		->set('stdhstate', $student->get('stdhstate'))
		->set('stdhzip', $student->get('stdhzip'))
		->set('stdphoto', $student->get('stdphoto'))
		->set('cmname', $student->get('cmname'))
		->set('cmnamelf', $student->get('cmnamelf'))
		->set('cmphone', $student->get('cmphone'))
		->set('screenURL', '/apps/idea/iep/desktop/desk_next.php')
		->set('refresh_screen_js', 'parent.reloadTree();')
		->getKey();

	$std_title = IDEAStudentCaption::get($tsRefID);
	$str = '';
	$allblocks = IDEAFormat::getDocBlocks();

	for ($i = 0; $i < count($allblocks); $i++) {
		if (checkBlock($tsRefID, $student->get('stdiepyear'), $allblocks[$i]['iepinclude']) == 1) {
			$str .= $allblocks[$i]['iepnum'] . ',';
		}
	}

	$url = CoreUtils::getURL(IDEAFormat::get('gen_file'), array('str' => $str, 'dskey' => $dskey, 'format' => 'pdf', 'type' => 3,));

	$tw = new UITreeWrapper(CoreUtils::getURL('desc_tree.ajax.php', array('dskey' => $dskey)));

	$tw->collapseRightFrame(true);

	$tw->expandFirstLevel();

	$tw->addItemProcess(
		UITreeWrapperCategory::ALL_TREE_ITEMS,
		'javascript: openApp()'
	);

	echo $tw->toHTML();

	io::jsVar('url', $url);
	io::jsVar('dskey', $dskey);
	io::jsVar('title', IDEAFormat::getIniOptions('iep_year_title'));

?>
<script type="text/javascript">
	api.window.changeTitle(<?=json_encode($std_title);?>);

	if (SystemCore.getUserInterface().coreVersion == 1) {
		parent.zWindow.appIcon = 'wbs_stdman_main';
		parent.zWindow.changeSystemBarCaption("<? print addslashes($student->get('stdname'));?>");
		parent.zWindow.changeCaption("<? print addslashes($std_title);?>");
	} else {
		api.window.changeTitle(<?= json_encode($std_title) ?>);
	}

	function openApp() {
		var item = UITree.get().getSelectedItem();
		if (item) {
			if (item.value() == 'prevIEP') {
				//load PDF
				api.ajax.process(UIProcessBoxType.REPORT, url);
				return;
			}
			if (item.value() == 'errorIEPYear') {
				//show error
				api.error('Please, create ' + title, onOk);
			} else {
				// load app
				UITreeWrapper.get().loadPage(api.url(item.value(), {'dskey': dskey}));
			}
		}
	}

	function onOk() {
		UITree.get().selectItemByCaption(title);
	}

	function reloadTree(mode) {
		api.ajax.post(
			'refresh_dskey.ajax.php',
			{'dskey': dskey},
			function (answer) {
				UITreeWrapper.get().reload();
			}
		);
	}

	function selectNext(mode) {
		var tree = UITree.get();
		var selectItem = tree.getSelectedItem();
		var items = selectItem.parentItem().getItems();

		// search number active item
		for (var i = 0; selectItem.caption() != items[i].caption(); i++) {

		}
		if (items.length != i + 1) {
			if (mode == 1) {
				var inItems = items[i + 1].getItems();
				tree.selectItemByCaption(inItems[0].caption());
			} else {
				var nextItem = items[i + 1].caption();
				tree.selectItemByCaption(nextItem);
			}
		} else {
			tree.selectItemByCaption(selectItem.parentItem().caption());
			selectNext(1);
		}
	}

</script>
