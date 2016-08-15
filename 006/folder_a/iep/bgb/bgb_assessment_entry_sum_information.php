<?php

	Security::init();

	$en_refid = io::geti('en_refid');
	$as_refid = io::geti('as_refid');

	$trial_num = db::execSQL("
		SELECT trial_num
		  FROM webset.std_bgb_assessment_entry
		 WHERE en_refid = $en_refid
		   AND as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$result = db::execSQL("
		SELECT ind.ind_symbol,
			   COUNT(ind.ind_symbol)
		  FROM webset.std_bgb_measurement_benchmark AS mb
			   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
		       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
		       INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
		 WHERE " . ($en_refid != -1 ? "mb.en_refid = $en_refid AND" : "" ) . "
		       ind.as_refid = $as_refid
		   AND ind.vndrefid = VNDREFID
		   AND m.type_measure = 'Measurable'
		 GROUP BY ind.ind_symbol
	")
	->keyedCol();

	$met_mastery = db::execSQL("
		SELECT ind.met_mastery,
			   COUNT(ind.met_mastery)
		  FROM webset.std_bgb_measurement_benchmark AS mb
			   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
		       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
		       INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
		 WHERE " . ($en_refid != -1 ? "mb.en_refid = $en_refid AND" : "" ) . "
		   	   ind.as_refid = $as_refid
		   AND ind.vndrefid = VNDREFID
		   AND m.type_measure = 'Measurable'
		 GROUP BY ind.met_mastery
	")
	->keyedCol();

	$met_mastery['Y'] = array_key_exists('Y', $met_mastery) ? $met_mastery['Y'] : 0;
	$met_mastery['N'] = array_key_exists('N', $met_mastery) ? $met_mastery['N'] : 0;

	$met_mastery_pc = round ($met_mastery['Y'] * 100 / ($met_mastery['Y'] + $met_mastery['N']), 2);

	$edit = new EditClass('edit', 0);

	$edit->title = 'Measurement Summary Information' . ($en_refid != -1 ? '( Trial #' . $trial_num . ')' : '');

	$edit->addGroup('Common Information');
	$edit->addControl('M %', 'protected')
		->value($met_mastery_pc . '%');

	$edit->addControl('Mastery Met', 'protected')
		->value($met_mastery['Y']);

	$edit->addControl('Not Met', 'protected')
		->value($met_mastery['N']);

	$char_pie = new ChartPie('Measurement Summary Information', 220, 220, ChartPie::LEFT);

	$edit->addGroup('Scoring Distribution');

	foreach ($result as $key => $value) {
		$edit->addControl("Indicator ($key)", 'protected')
			->value($value);

		$char_pie->addPoint($value, $key);
	}

	$append = UILayout::factory()
		->addObject($char_pie);
	$edit->addControl('', 'protected')
		->append($append);

	$edit->firstCellWidth = '15%';

	$edit->printEdit();
?>