<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$studentTN = IDEAStudentTN::factory($tsRefID);

	$grefid = io::geti('RefID');

	$SQL = "
		SELECT gsentance
		  FROM webset.std_bgb_goal
		 WHERE grefid = " . $grefid . "
	";
	$gsentance = db::execSQL($SQL)->getOne();

	$values = array(
		'C' => 'Child Entring',
		'F' => 'Family Entring',
		'CF' => 'Child and Family Entring'
	);

	$edit = new EditClass('edit', $grefid);
	$edit->title = $gsentance;
	$edit->firstCellWidth = '30px';

	$edit->addGroup('Services to be Provided (required by Part C)');
	$serv = $studentTN->getServices();
	$stn_refids = array_keys($serv);

	$SQL = "
		SELECT stn_refid, stsr_provided
		  FROM webset.std_tn_serv_summ_rc
		 WHERE grefid = " . $grefid . ";
	";
	$stsr = db::execSQL($SQL)->assocAllKeyed();

	foreach ($serv as $key => $ser) {
		$short = false;
		$caption = $ser['nsdesc'];
		if (strlen($ser['nsdesc']) > 60) {
			$short = true;
			$caption =  str::substr($ser['nsdesc'], 0, 60) . '...';
		}
		$control = $edit->addControl(
				FFSelect::factory(
					$caption .
					($short ?
						FileUtils::getIMGFile('view.png')
							->css('cursor', 'pointer')
							->hint($ser['nsdesc'])
							->toHTML()
						: '')
				)
			)
			->name('stsr_provided_' . $key)
			->data($values)
			->append(
				FFInput::factory('stn_refid')
					->hide(true)
					->name('stn_refid_' . $key)
					->value($ser['stn_refid'])
			);

		if (array_key_exists($key, $stsr)) {
			$control->value($stsr[$key]['stsr_provided']);
		}

		$control->emptyOption(true);
	}

	$edit->addGroup('Non-req. Services');

	$justifications = $studentTN->getJustificationForProvision();
	$sns_refids = array_keys($justifications);

	$SQL = "
		SELECT sns_refid, stsn_provided
		  FROM webset.std_tn_serv_summ_nr
		 WHERE grefid = " . $grefid . ";
	";
	$stsn = db::execSQL($SQL)->assocAllKeyed();

	foreach ($justifications as $key => $val) {

		$short = false;
		$caption = $val['txt01'];
		if (strlen($val['txt01']) > 60) {
			$short = true;
			$caption =  str::substr($val['txt01'], 0, 60) . '...';
		}
		$control = $edit->addControl(
			FFSelect::factory(
				$caption .
				($short ?
					FileUtils::getIMGFile('view.png')
						->css('cursor', 'pointer')
						->hint($val['txt01'])
						->toHTML()
					: '')
			)
		)
			->name('stsn_provided_' . $key)
			->data($values)
			->append(
				FFInput::factory('sns_refid')
					->hide(true)
					->name('sns_refid_' . $key)
					->value($key)
			);

		if (array_key_exists($key, $stsn)) {
			$control->value($stsn[$key]['stsn_provided']);
		}
		$control->emptyOption(true);
	}

	$edit->setPostsaveCallback(
		'saveProvided',
		'./outcome_services_save.inc.php',
		array(
			'stn_refids' => $stn_refids,
			'sns_refids' => $sns_refids
		)
	);

	$edit->saveAndAdd = false;

	$edit->printEdit();

	//echo FFRadioList::factory('test')->data(array('a' => 'a', 'b' => 'b'))->value('b    ')->toHTML();
?>