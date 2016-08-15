<?php

	Security::init();

	$area = io::get('area');
	$where = '';

	if (in_array($area, array('D', 'R'))) {
		$where .= " AND (screfid = " . VNDState::factory()->id . " or screfid = -1) ";
	}

	$SQL = "
        SELECT smirefid,
               COALESCE(state, 'ALL') as state,
               smaname as category,
               sminame as sminame,
               sqltable,
               coreurl,
               staterefid,
               smarefid
          FROM webset.sped_sm_items
               LEFT JOIN webset.sped_sm_area USING(smarefid)
               LEFT JOIN webset.glb_statemst ON glb_statemst.staterefid = screfid
         WHERE area = '" . $area . "'
           AND (webset.sped_sm_items.expdate>now() OR webset.sped_sm_items.expdate IS NULL)
               " . $where . "
         ORDER BY COALESCE(state, 'ALL'), webset.sped_sm_area.seqnum, smaname, sped_sm_items.seqnum, sminame
    ";
	$items = db::execSQL($SQL)->assocAll();

	$state = '';
	$category = '';
	$tree = new UITree();
	for ($i = 0; $i < count($items); $i++) {
		if ($items[$i]['state'] != $state) {
			$state = $items[$i]['state'];
			$s = $tree->addItem($state);
			$s->opened(true);
			$category = '';
		}
		if ($items[$i]['category'] != $category) {
			$category = $items[$i]['category'];
			$c = $s->addItem($category);
			$c->opened(true);
		}
		$file =  '/apps/idea/' . $items[$i]['coreurl'];
		if (file_exists(SystemCore::$physicalRoot .  preg_replace('@\?.*@', '', $file))) {
			$c->addItem($items[$i]['sminame'],  $file, FileUtils::getFileIcon('paper'));
		} else {
			$c->addItem('<i>' . $items[$i]['sminame'] . '</i>', '', FileUtils::getFileIcon('questionnaire1'));
		}
	}

	$tree->toAJAX();
?>
