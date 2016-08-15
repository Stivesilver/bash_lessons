<?PHP
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$tree = new UITree();

	// --- Fetch Categories
	$sql = "
		SELECT siymrefid,
			   TO_CHAR(siymiepbegdate, 'mm/dd/yyyy') || ' - ' || TO_CHAR(siymiependdate, 'mm/dd/yyyy') AS year_period
		  FROM webset.std_iep_year
		 WHERE stdrefid = $tsRefID 
		   AND siymrefid != $stdIEPYear 
		 ORDER BY siymiepbegdate DESC 
	";

	$rows = db::execSQL($sql)
		->assocAll();

	foreach ($rows as $row) {
		$tree->addItem($row['year_period'], $row['siymrefid'])
			->category('yr')
			->icon('');
	}

	echo $tree->toAJAX();
?>
