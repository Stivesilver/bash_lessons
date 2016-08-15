<?php
	Security::init();

	$data = IDEAData::factory()->xmlImport(
		$_POST['template'],
		io::post('root_id'),
		$_POST['importdata']
	);

?>
