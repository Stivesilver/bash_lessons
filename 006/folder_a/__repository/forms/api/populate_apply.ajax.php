<?PHP
	Security::init();

	$param = io::post('param');
	$param = json_decode($param, true);
	$nfds = DataStorage::factory($param['fkey']);
	$values = $nfds->get('values');

	$ofds = DataStorage::factory($param['ofkey']);
	$ofds->set('values', $values);
?>
