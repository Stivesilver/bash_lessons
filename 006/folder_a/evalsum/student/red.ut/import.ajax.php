<?PHP
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$tree = new UITree();

	// --- Fetch Categories
	$sql = "
		SELECT eprefid,
			   TO_CHAR(date_start, 'mm/dd/yyyy') AS process
		  FROM webset.es_std_evalproc
		 WHERE stdrefid = $tsRefID
		   AND eprefid != $evalproc_id
		 ORDER BY date_start DESC
	";

	$rows = db::execSQL($sql)
		->assocAll();

	foreach ($rows as $row) {
		$tree->addItem($row['process'], $row['eprefid'])
			->category('pr')
			->icon('');
	}

	echo $tree->toAJAX();
?>
