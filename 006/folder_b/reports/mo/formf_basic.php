<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form F: Accommodations and Modifications';
	$list->showSearchFields = true;
	$list->printable = false;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE);

	$list->SQL = "
		SELECT * FROM
			(SELECT DISTINCT ON (ts.tsrefid, prm.stsrefid)
				   " . IDEAParts::get('stdname') . " AS stdname,
			       CASE
			       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
			       ELSE 'N'
			       END AS stdstatus,
			       CASE
			       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
			       ELSE 'N'
			       END AS spedstatus,
			       macdesc,
			       stsdesc,
			       prm.stsrefid,
			       ts.tsrefid,
			       seq_num,
			       stsseq
			  FROM webset.std_progmod AS prm
			       INNER JOIN webset.sys_teacherstudentassignment AS ts ON (prm.stdrefid = ts.tsrefid)
			       " . IDEAParts::get('studentJoin') . "
			       INNER JOIN webset.statedef_mod_acc AS acc ON (acc.stsrefid = prm.stsrefid)
			       INNER JOIN webset.statedef_mod_acc_cat AS cat ON (cat.macrefid = acc.macrefid)
			 WHERE std.vndrefid = VNDREFID ADD_SEARCH
			 ORDER BY ts.tsrefid) AS t1
			 ORDER BY t1.stdname, t1.seq_num, t1.stsseq, t1.stsdesc
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory())->sqlField('ts.umrefid');
	$list->addSearchField(FFIDEAGradeLevel::factory())->sqlField('std.gl_refid');
	$list->addSearchField(FFSelect::factory('Modification/Accommodation'))
		->sqlField('prm.stsrefid')
		->sql("
			SELECT stsrefid,
			       macdesc || ': ' || stsdesc
			  FROM webset.statedef_mod_acc acc
			       INNER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = acc.macrefid
			 WHERE acc.screfid = " . VNDState::factory()->id . "
			   AND (recactivationdt IS NULL OR now()< recactivationdt)
			 ORDER BY seq_num, stsseq, stsdesc
        ");
	$list->addSearchField(FFSelect::factory('Location'))
		->sqlField("prm.val_id = 'ADD_VALUE' AND typeofval='loc'")
		->sql("
			SELECT malrefid,
			       maldesc
			  FROM webset.statedef_mod_acc_loc
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recactivationdt IS NULL OR now()< recactivationdt)
			 ORDER BY seq_num, maldesc
        ");
	$list->addSearchField(FFSelect::factory('Frequency'))
		->sqlField("
			EXISTS (SELECT 1
                         FROM webset.std_progmod AS fpr
                        WHERE fpr.stdrefid = ts.tsrefid
                          AND fpr.val_id = 'ADD_VALUE' AND typeofval='frq'
                          AND fpr.stsrefid = acc.stsrefid)
		")
		->sql("
			SELECT esfumrefid,
			       esfumdesc,
			       CASE esfumdesc
			       WHEN 'Daily' THEN 1
			       WHEN 'Weekly' THEN 2
			       WHEN 'Monthly' THEN 3
			       WHEN 'Other' THEN 4
			       END
			  FROM webset.statedef_esy_serv_freq_unit_of_measur
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (
					   esfumdesc LIKE 'Daily'
					OR esfumdesc LIKE 'Weekly'
					OR esfumdesc LIKE 'Monthly'
					OR esfumdesc LIKE 'Other'
			       )
			 ORDER BY 3
        ");
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Modifications/Accommodations')->dataCallback('accomm');
	$list->addColumn('Location')->dataCallback('loc');
	$list->addColumn('Frequency')->dataCallback('freq');
	$list->addColumn('Begin Date')->dataCallback('begdate');
	$list->addColumn('End Date')->dataCallback('enddate');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();

	function accomm($data, $col) {
		$accs = db::execSQL("
			SELECT val
			  FROM webset.std_progmod AS pm
			 WHERE stdrefid = " . $data['tsrefid'] . "
			   AND typeofval = 'oth'
			   AND pm.stsrefid = " . $data['stsrefid'] . "
		")->assocAll();
		$res = $data['macdesc'] . ': ' . $data['stsdesc'];
		$i = 1;
		foreach ($accs as $acc) {
			if ($i == 1) {
				$res .= $acc['val'];
			} else {
				$res .= ', ' . $acc['val'];
			}
			$i++;
		}
		return $res;
	}

	function loc($data, $col) {
		$locs = db::execSQL("
			SELECT maldesc
			  FROM webset.std_progmod AS pm
			       INNER JOIN webset.statedef_mod_acc_loc ON val_id::INT = malrefid
			 WHERE stdrefid = " . $data['tsrefid'] . "
			   AND typeofval = 'loc'
			   AND pm.stsrefid = " . $data['stsrefid'] . "
			 ORDER BY seq_num, maldesc
		")->assocAll();
		$res = '';
		$i = 1;
		foreach ($locs as $loc) {
			if ($i == 1) {
				$res .= $loc['maldesc'];
			} else {
				$res .= ', ' . $loc['maldesc'];
			}
			$i++;
		}
		return $res;
	}

	function freq($data, $col) {
		$freqs = db::execSQL("
			SELECT esfumdesc
			  FROM webset.std_progmod AS pm
			       INNER JOIN webset.statedef_esy_serv_freq_unit_of_measur ON val_id::INT = esfumrefid
			 WHERE stdrefid = " . $data['tsrefid'] . "
			   AND typeofval = 'frq'
			   AND pm.stsrefid = " . $data['stsrefid'] . "
			 ORDER BY
				   CASE esfumdesc
				   WHEN 'Daily' THEN 1
				   WHEN 'Weekly' THEN 2
				   WHEN 'Monthly' THEN 3
				   WHEN 'Other' THEN 4
				   END
		")->assocAll();
		$res = '';
		$i = 1;
		foreach ($freqs as $freq) {
			if ($i == 1) {
				$res .= $freq['esfumdesc'];
			} else {
				$res .= ', ' . $freq['esfumdesc'];
			}
			$i++;
		}
		return $res;
	}

	function begdate($data, $col) {
		$begs = db::execSQL("
			SELECT val
			  FROM webset.std_progmod AS pm
			 WHERE stdrefid = " . $data['tsrefid'] . "
			   AND typeofval = 'beg'
			   AND pm.stsrefid = " . $data['stsrefid'] . "
		")->assocAll();
		$res = '';
		$i = 1;
		foreach ($begs as $beg) {
			if ($i == 1) {
				$res .= $beg['val'];
			} else {
				$res .= ', ' . $beg['val'];
			}
			$i++;
		}
		return $res;
	}

	function enddate($data, $col) {
		$ends = db::execSQL("
			SELECT val
			  FROM webset.std_progmod AS pm
			 WHERE stdrefid = " . $data['tsrefid'] . "
			   AND typeofval = 'end'
			   AND pm.stsrefid = " . $data['stsrefid'] . "
		")->assocAll();
		$res = '';
		$i = 1;
		foreach ($ends as $end) {
			if ($i == 1) {
				$res .= $end['val'];
			} else {
				$res .= ', ' . $end['val'];
			}
			$i++;
		}
		return $res;
	}

?>
