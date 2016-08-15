<?PHP
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid = $ds->safeGet('stdrefid');
	$state_id = io::geti('state_id');
	$fkey = io::get('ofkey');
	$form = IDEAForm::factory($fkey);
	$std_id = $form->getParameter('std_id');

	if ($std_id) {
		$where = "AND sfrefid != $std_id";
	} else {
		$where = "";
	}

	$tree = new UITree();

	// --- Fetch Categories
	$sql = "
		SELECT sfrefid,
			   std.lastuser || ', ' || TO_CHAR(std.lastupdate, 'MM/DD/YYYY') AS finfo
		  FROM webset.std_forms_xml std
			   INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.tsrefid
		 	   INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
			   INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.MFCpRefId
			   LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = std.iepyear
		 WHERE ts.stdrefid = $stdrefid
		   AND std.frefid = $state_id
		   $where
		 ORDER BY sfrefid DESC
	";

	$rows = db::execSQL($sql)
		->assocAll();

	foreach ($rows as $row) {
		$tree->addItem($row['finfo'], $row['sfrefid'])
			->category('yr')
			->icon('');
	}

	echo $tree->toAJAX();
?>
