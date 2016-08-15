<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	io::jsVar('dskey', $dskey);

	io::js('var other = {};');

	$tree = new UITree();

	$tree->defaultIcon('');

	io::css('.item', 'margin: 1px 0; padding: 1px 1px 1px 1px;');

	# enable Flag Mode
	$tree->flagMode();

	$sql = "
		SELECT sbj.code,
               sbj.progdesc,
               acc.accrefid,
               cat.catdesc || ': ' || COALESCE(acc.acccode || ' - ','') || acc.accdesc AS accdesc,
               acc.accdesc AS other,
               std.refid AS stdrefid,
               std.acc_oth AS other_val
          FROM webset.statedef_aa_acc acc
               INNER JOIN webset.statedef_aa_prog sbj ON sbj.code = acc.cat
               LEFT JOIN webset.statedef_aa_cat AS cat ON cat.catrefid = acc.acccat
               LEFT JOIN webset.std_form_d_acc std ON (acc.accrefid = std.accrefid AND std.stdrefid = $tsRefID AND std.syrefid = $stdIEPYear)
         ORDER BY seqnum, acc.seq_num
	";

	$rows = db::execSQL($sql)->assocAll();

	$categs = array();

	if (!is_array($rows) || count($rows) < 1) {
		echo $tree->toHTML();

		die();
	}

	foreach ($rows as $row) {
		$categs[$row['code']]['code'] = $row['code'];
		$categs[$row['code']]['progdesc'] = $row['progdesc'];

		if (isset($row['code'])) {
			$categs[$row['code']]['items'][$row['accrefid']]['accrefid'] = $row['accrefid'];
			$categs[$row['code']]['items'][$row['accrefid']]['accdesc'] = $row['accdesc'];
			$categs[$row['code']]['items'][$row['accrefid']]['stdrefid'] = $row['stdrefid'];
			$categs[$row['code']]['items'][$row['accrefid']]['other'] = $row['other'];
			$categs[$row['code']]['items'][$row['accrefid']]['other_val'] = $row['other_val'];
		}
	}

	foreach ($categs as $category) {
		$categoryBrunch = $tree->addItem($category['progdesc'])
			->category('cat')
			->icon('')
			->selectable(false);

		if (isset($category['items']) && count($category['items']) > 0) {
			foreach ($category['items'] as $item) {
				if (strtolower($item['other']) == 'other:') {
					$data = UILayout::factory()
						->addHTML(
							$item['accdesc'],
							UILayoutAttr::factory('', UILayoutAttr::CELL)
								->onClick('addFlag(' . json_encode($item['accrefid']) . ');')
						)
						->addObject(
							FFInput::factory()
								->grayText('Specify...')
								->value($item['other_val'])
								->htmlWrap('')
								->css('width', '250px')
								->onChange('other[' . json_encode($item['accrefid']) . '] = $(this).val();')
								->onClick('addFlag(' . json_encode($item['accrefid']) . ', 1);')
								->onBlur('this.onchange.call(this, event);')
							, '[padding-left: 5px;]')
						->toHTML();
				} else {
					$data = UILayout::factory()
						->addHTML(
							$item['accdesc'],
							UILayoutAttr::factory('', UILayoutAttr::CELL)
								->onClick('addFlag(' . json_encode($item['accrefid']) . ');')
						)
						->toHTML();
				}
				$categoryBrunch
					->addItem($data, $item['accrefid'])
					->className('zLightBox border zRound6 item')
					->category('it')
					->selectable(false);
				if ($item['stdrefid']) {
					$tree->setFlags(array($item['accrefid'], $category['code']));
				}
			}
		}
	}


	echo UIFrameSet::factory('100%', 'auto, *40')
		->addFrame(
			UIFrame::factory()
				->className('zBox10 zLightLines zRound6')
				->scrollable(true)
				->indent(5)
				->addObject($tree)
		)
		->addFrame(
			UIFrame::factory()
				->indent(0, 5, 5, 5)
				->addObject(
					UILayout::factory()
						->addObject(
							FFButton::factory('Cancel', 'api.reload();')
								->width('110px')
							, '1px left [padding: 2px]'
						)
						->addHTML('')
						->addObject(
							IDEAFormat::getPrintButton(array('dskey' => $dskey))
							, '1px right [padding: 2px]'
						)
						->addObject(
							FFButton::factory('Save & Finish', 'save(1, ' . $nexttab . ')')
								->width('110px')
							, '1px right [padding: 2px]'
						)
						->addObject(
							FFButton::factory('Save & Edit', 'save()')
								->width('110px')
							, '1px right [padding: 2px]'
						)
				)
		)
		->toHTML();
?>

<script>
	function save(finish, nexttab) {
		var selIt = UITree.get().getNestedFlaggedItems();
		var res = new Array();
		for (var index = 0; index < selIt.length; ++index) {
			if (selIt[index].value()) {
				res.push(selIt[index].value());
			}
		}
		var url = api.url('part2add.ajax.php', {'res': res.join(','), 'dskey': dskey, 'other': JSON.stringify(other)});
		api.ajax.process(
				UIProcessBoxType.PROCESS,
				url,
				'',
				true
			).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				if (finish == 1) {
					javascript:parent.switchTab(nexttab);
				}
			}
		)
	}

	function addFlag(accrefid, input) {
		var item = UITree.get().getItemByValue(accrefid);
		if (item.flagged() && input != 1) {
			item.flagged(false);
		} else {
			item.flagged(true);
		}
	}
</script>
