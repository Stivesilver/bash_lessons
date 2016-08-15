<?php

	Security::init();

	$bench_id = io::geti('bench_id');
	$templ_id = io::geti('templ_id');

	DBImportRecord::factory('webset.std_bgb_measure_test')
		->set('templ_id', $templ_id)
		->set('bench_id', $bench_id)
		->setUpdateInformation()
		->import();

?>
