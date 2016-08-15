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

	function getCaption(IDEAStudent $student) {
		$stdid_header = '';
		$stdname = '';
		$cmname = '';
		$evalproc = '';
		if (IDEACore::disParam(11) == 'Y' && $student->get('stdschid') != '') {
			$stdid_header = 'ID: <i>' . $student->get('stdschid') . '</i>';
		}
		$stdname = $student->get('stdname');
		if ($student->get('cmnamelf') != '') {
			$cmname = 'Case Manager: <i>' . $student->get('cmnamelf') . '</i>';
		}
		if ($student->get('stdiepyearbgdt') != '' || $student->get('stdiepyearendt') != '') {
			$evalproc = 'Current Evaluation: <i>' . $student->get('evalproc_type') . ' - ' . CoreUtils::formatDateForUser($student->get('evalproc_date_start')) . '</i>';
		}
		$mask = '%D, %S - %C, %E';
		$replace_lbl = array('%D', '%S', '%C', '%E');
		$replace_val = array($stdid_header, $stdname, $cmname, $evalproc);
		$caption = str_replace($replace_lbl, $replace_val, $mask);
		$caption = trim($caption,'-, ');
		return $caption;
	}

	$tsRefID = CryptClass::factory()->decode(io::get('tsRefID'));
	$student = new IDEAStudentEval($tsRefID);

	$dskey = DataStorage::factory()
		->set('tsRefID', $tsRefID)
		->set('evalproc_id', $student->get('evalproc_id'))
		->set('evalproc_type', $student->get('evalproc_type'))
		->set('evalproc_date_start', $student->get('evalproc_date_start'))
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

	$std_title = getCaption($student);

	$tw = new UITreeWrapper(CoreUtils::getURL('desc_tree.ajax.php', array('dskey' => $dskey, 'screenID' => io::get('screenID'))));

	$tw->leftFrameWidth('20%');
	$tw->className('zHLightBox');

//	$tw->collapseRightFrame(true);

	$tw->addItemProcess(
		UITreeWrapperCategory::ALL_TREE_ITEMS,
		'javascript: openApp()'
	);

	echo $tw->toHTML();

	if (io::get('eval_set') == 'sel') {
		io::js("
			var tree = UITree.get();
			tree.addEventListener(
				UITreeEvent.LOADED,
				function(e) {
					tree.selectItemByCaption('Select Evaluation Process');
				},
				this
			);
		");
	} elseif (io::get('eval_set') == 'add') {
		io::js("
			var tree = UITree.get();
			tree.addEventListener(
				UITreeEvent.LOADED,
				function(e) {
					tree.selectItemByCaption('New Evaluation Process');
				},
				this
			);"
		);
	}

	io::jsVar('dskey', $dskey);
	io::jsVar('title', 'Evaluation Process');

?>
<script type="text/javascript">

	if (SystemCore.getUserInterface().coreVersion == 1) {
		parent.zWindow.appIcon = 'ev_evalmgr';
		parent.zWindow.changeSystemBarCaption("<? print addslashes($student->get('stdname'));?>");
		parent.zWindow.changeCaption("<? print addslashes($std_title);?>");
	} else {
		api.window.changeTitle(<?= json_encode($std_title) ?>);
	}

	function openApp() {
		var item = UITree.get().getSelectedItem();
		if (item) {
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
