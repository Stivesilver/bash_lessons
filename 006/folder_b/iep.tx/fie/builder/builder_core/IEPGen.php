<?PHP

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$needed_block = null;

	foreach (explode(',', io::get('str')) as $key => $value) {
		if ($value > 0) {
			$needed_block = $value;
			break;
		}
	}

	if ($needed_block > 0) {
		$block_id = db::execSQL("
			SELECT ieprefid
              FROM webset.sped_iepblocks
             WHERE iepnum = " . $needed_block . "
               AND ieptype = 36
		")->getOne();
	}

	$doc = new IDEAIepBuilder(IDEABlockBuilder::FIE, $tsRefID);

	$doc->setSelectedBlocks($block_id);
	//$doc->setHeaderDoc('Date of FIE: ', io::post('fie_date'));
	$doc->addBlocks();

	$doc->getRCDoc()->open();


?>