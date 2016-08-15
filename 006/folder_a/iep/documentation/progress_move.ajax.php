<?php

	Security::init();
	$siymrefid = io::geti('siymrefid');
	$per_source = io::geti('per_source');
	$per_dest = io::geti('per_dest');
	$esy = io::get('esy');
	$SQL = "
       SELECT * FROM (SELECT spr.spr_period_data AS period_data,
                             spr_refid
                        FROM webset.std_bgb_goal goal
                             INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
                             INNER JOIN webset.std_progress_reporting spr ON goal.grefid = spr.sbg_grefid
                       WHERE baseline.siymrefid = " . $siymrefid . "
                             AND baseline.esy = '" . $esy . "'
                       UNION ALL
                      SELECT spr.spr_period_data AS period_data,
                             spr_refid
                        FROM webset.std_bgb_goal goal
                             INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
                             INNER JOIN webset.std_bgb_benchmark benchmark ON goal.grefid = benchmark.grefid
                             INNER JOIN webset.std_progress_reporting spr ON spr.sbb_brefid = benchmark.brefid
                       WHERE baseline.siymrefid = " . $siymrefid . "
                             AND baseline.esy = '" . $esy . "'
                     ) as t
    ";
	$data = db::execSQL($SQL)->assocAll();

	foreach ($data as $row) {
		$extentProgress = json_decode($row['period_data'], true);
		if (isset($extentProgress[$per_dest])) {
			io::ajax('finish', '0');
			die();
		}
	}
	foreach ($data as $row) {
		$extentProgress = json_decode($row['period_data'], true);
		if (isset($extentProgress[$per_source])) $extentProgress[$per_dest] = $extentProgress[$per_source];
		unset($extentProgress[$per_source]);
		DBImportRecord::factory('webset.std_progress_reporting', 'spr_refid')
			->key('spr_refid', $row['spr_refid'])
			->set('spr_period_data', json_encode($extentProgress))
			->import();
	}

	io::ajax('finish', '1');

?>