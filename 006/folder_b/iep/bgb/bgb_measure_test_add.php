<?php

	Security::init();

	$benchmark_id = io::geti('benchmark_id');

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tsRefID = $ds->safeGet('tsRefID');
	$year = io::geti('year');
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$vourefid = $ds->safeGet('vourefid');

	io::jsVar('dskey', $dskey);
	io::jsVar('siymrefid', $siymrefid);
	io::jsVar('tsRefID', $tsRefID);

	$edit = new EditClass("edit1", io::get("RefID"));

	$edit->title = 'Add/Edit Template';

	$edit->setSourceTable('webset.std_bgb_measure_test', 'mtrefid');

	$edit->addGroup("General Information");
	$edit->addControl("Sentence Preface", "select")
		->sqlField('templ_id')
		->name('templ_id')
		->sql("
            SELECT it.mirefid,
		           ct.name || ' - ' || it.name AS name
		      FROM webset.disdef_bgb_measure_items AS it
		           INNER JOIN webset.disdef_bgb_measure_cat AS ct
		                      ON (ct.mcrefid = it.cat_id)
		     WHERE vndrefid = VNDREFID
		       AND (CASE WHEN NOW() > it.end_date THEN 'N' ELSE 'Y' END)::VARCHAR = E'Y'
		       AND (CASE WHEN NOW() > ct.end_date THEN 'N' ELSE 'Y' END)::VARCHAR = E'Y'
		     ORDER BY ct.order_num, ct.name, it.order_num, it.name
        ");

	$edit->addControl('Graph Title')->sqlField('name');

	$edit->addControl('Max Value', 'int')->sqlField('max_points')->req();

	$edit->addUpdateInformation();
	$edit->addControl('Benchmark ID', 'hidden')->value($benchmark_id)->sqlField('bench_id');

	$edit->finishURL = CoreUtils::getURL('bgb_measure_test_list.php', array('dskey' => $dskey, 'benchmark_id' => $benchmark_id));
	$edit->cancelURL = CoreUtils::getURL('bgb_measure_test_list.php', array('dskey' => $dskey, 'benchmark_id' => $benchmark_id));

	$edit->saveAndAdd = false;

	$edit->printEdit();
?>
