<?PHP
	Security::init();

	$tsRefid = io::get('tsrefid');
	$table = io::get('table');
	$search_key = io::get('search_key');
	$search_id = io::get('search_id');

	$tree = new UITree();

	# This block adds where if we use backup not for Students. 
	# Note that we search simple and base64 encoded id
	# Encoded was added later because to support xml-texts
	# On wrong xml-texts outer backup xml fails too 
	$where = '';
	if (strlen($search_key) > 0) {
		$where = "
			AND ENCODE(DECODE(content, 'base64'),'escape') SIMILAR TO 
			'%<value name=\"" . $search_key . "\">(" . base64_encode($search_id) . "|" . $search_id . ")</value>%'";
	}

	// --- Fetch Categories
	$sql = "
		SELECT refid,
		       lastuser || ' ' || to_char(lastupdate, 'mm-dd-yyyy hh:mi AM') AS bname
		  FROM webset.std_backup
		 WHERE COALESCE(stdrefid, 0) = " . (int)$tsRefid . "
		   AND area = '" . $table . "'
				" . $where . "
		 ORDER BY refid DESC
	";

	$rows = db::execSQL($sql)
		->assocAll();

	foreach ($rows as $row) {
		$tree->addItem($row['bname'], $row['refid'])
			->category('yr')
			->icon('');
	}

	echo $tree->toAJAX();
?>
