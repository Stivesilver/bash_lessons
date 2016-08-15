<?PHP
	Security::init();

	$param = io::post('param');
	$param = json_decode($param, true);
	$dskey = $param['dskey'];
	$ds = DataStorage::factory($dskey);
	$ds->set('constr_refid', $param['refid']);
?>
