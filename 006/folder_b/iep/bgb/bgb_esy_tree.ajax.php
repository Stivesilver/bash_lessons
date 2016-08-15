<?PHP
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$tree = new UITree();

	// --- Fetch Categories
	$bsql = "
		SELECT blrefid,
               COALESCE(order_num::VARCHAR || '. ', '')  || " . IDEAParts::get('baselineArea') . " AS name
          FROM webset.std_bgb_baseline std
               INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON std.blksa = ksa.gdskrefid
               INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
               INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
         WHERE stdrefid = " . $tsRefID . "
           AND std.siymrefid = " . $stdIEPYear . "
           AND std.esy = 'N'
         ORDER BY std.order_num, blrefid
	";

	$baselins = db::execSQL($bsql)
		->assocAll();

	foreach ($baselins as $bsl) {
		$goalitem = $tree->addItem(UILayout::factory()->addHTML($bsl['name'], '[white-space:normal;]')->toHTML(), $bsl['blrefid'])
			->category('bs')
			->icon('');

		$gsql = "
            SELECT grefid,
                   COALESCE(bl.order_num::VARCHAR || '.', '') || COALESCE(gl.order_num::VARCHAR, '') || ' ' || COALESCE(overridetext,gsentance) AS name
              FROM webset.std_bgb_goal AS gl
              	   INNER JOIN webset.std_bgb_baseline AS bl ON (gl.blrefid = bl.blrefid)
             WHERE gl.blrefid = " . $bsl['blrefid'] . "
             ORDER BY gl.order_num, grefid
        ";

		$goals = db::execSQL($gsql)
			->assocAll();

		foreach ($goals as $goal) {
			$becnhitem = $goalitem->addItem(UILayout::factory()->addHTML($goal['name'], '[white-space:normal;]')->toHTML(), $goal['grefid'])
				->category('gl')
				->icon('');

			$benchsql = "
				SELECT brefid,
	            	   COALESCE(bl.order_num::VARCHAR || '.', '') || COALESCE(gl.order_num::VARCHAR || '.', '') || COALESCE(bm.order_num::VARCHAR, '') || ' ' || COALESCE(bm.overridetext, bm.bsentance) AS name
	        	  FROM webset.std_bgb_benchmark AS bm
	        	 	   INNER JOIN webset.std_bgb_goal AS gl ON (bm.grefid = gl.grefid)
	        	  	   INNER JOIN webset.std_bgb_baseline AS bl ON (gl.blrefid = bl.blrefid)
	        	 WHERE bm.grefid = " . $goal['grefid'] . "
	             ORDER BY bm.order_num, brefid
			";

			$benchs = db::execSQL($benchsql)
				->assocAll();

			foreach ($benchs as $bench) {
				$becnhitem->addItem(UILayout::factory()->addHTML($bench['name'], '[white-space:normal;]')->toHTML(), $bench['brefid'])
					->category('bn')
					->icon('');
			}
		}
	}
	echo $tree->toAJAX();
?>
