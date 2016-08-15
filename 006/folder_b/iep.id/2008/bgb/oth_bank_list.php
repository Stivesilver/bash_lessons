<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey);
	$list  = new ListClass();

    $list->multipleEdit 	= false;
    $list->showSearchFields = true;
    $list->pageCount 	    = "10000";
    $list->title 			= "My (" . $_SESSION["s_userName"] . ") Goal Bank";
    $list->SQL 				= "
    	SELECT gdskrefid,
               " . IDEAParts::get('baselineArea') . ",
               (SELECT count(1) FROM webset.disdef_bgb_ksaconditions b WHERE ksa.gdskrefid = b.blksa and umrefid = " . $_SESSION["s_userID"] . " AND enddate IS NULL),
               (SELECT count(1) FROM webset.disdef_bgb_ksaksgoalactions b WHERE ksa.gdskrefid = b.gdskgrefid and umrefid = " . $_SESSION["s_userID"] . " AND enddate IS NULL),
               (SELECT count(1) FROM webset.disdef_bgb_scpksaksgoalcontent b WHERE ksa.gdskrefid = b.gdskgrefid and umrefid = " . $_SESSION["s_userID"] . " AND enddate IS NULL)
          FROM webset.disdef_bgb_goaldomainscopeksa ksa
               INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
               INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
         WHERE domain.vndrefid = VNDREFID
           AND (CASE ksa.enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
           ADD_SEARCH
         ORDER BY domain.gdsdesc, scope.gdssdesc, gdsksdesc
        ";

	$list->addSearchField("Area", '')
		 ->sqlField( "lower(COALESCE(domain.gdsdesc || ' -> ','') || COALESCE(scope.gdssdesc || ' -> ','') || gdsksdesc)  like '%' || lower(ADD_VALUE) || '%'");

	$list->addColumn("Area")
		 ->width('55%');

	$list->addColumn("Condition")
		->width('15%')
		->type("link")
		->param("javascript:loadarea(AF_REFID, 'condition')");

	$list->addColumn("Verb")
		->width('15%')
		->type("link")
		->param("javascript:loadarea(AF_REFID, 'verb')");

	$list->addColumn("Content")
		->width('15%')
		->type("link")
		->param("javascript:loadarea(AF_REFID, 'content')");

    $list->printList();

   	print FFInput::factory()->name('dskey')->value($dskey)->hide()->toHTML();

?>

<script type="text/javascript">

    function loadarea(areaID, area) {
        url = api.url('oth_items_list.php', {'area_id': areaID, 'dskey': $('#dskey').val(), 'area': area});
		api.window.open('Goal Bank Items', url)
			.addEventListener(
				WindowEvent.CLOSE,
				function(e) {
					ListClass.get().reload();
				}
			);
    }

</script>
