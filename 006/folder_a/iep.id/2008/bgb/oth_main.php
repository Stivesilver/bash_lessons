<?php

	Security::init();

	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
	$tsRefID 	= $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$esy	 	= io::get('ESY');
	$editUrl    = CoreUtils::getURL(
	  	 		  'oth_main_edit.php', 
	  	 		  array('dskey' => $dskey, 'ESY' => $esy));
    $list 		= new listClass("goal");
    
    $list->addURL          = $editUrl;
    $list->editURL         = $editUrl;
    $list->multipleEdit    = "no";
    $list->deleteTableName = "webset.std_oth_goals";
    $list->deleteKeyField  = "grefid";
    $list->title 		   = "Goals";
    $list->SQL 			   = "
    	SELECT gRefID,
       	       order_num,
          	   gdsksdesc as area,
               ". IDEAPartsID::get('goal_statement') .",
               ". IDEAPartsID::get('goal_procedure') .",
               (SELECT count(1) FROM webset.std_oth_objectives o WHERE o.grefid=g.grefid),
               order_num
       	  FROM webset.std_oth_goals g
               INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON g.area_id = ksa.gdskrefid
               LEFT OUTER JOIN webset.disdef_bgb_ksaconditions cond ON g.cond_id = cond.crefid
               LEFT OUTER JOIN webset.disdef_bgb_ksaksgoalactions verb ON g.verb_id = verb.gdskgarefid
               LEFT OUTER JOIN webset.disdef_bgb_scpksaksgoalcontent cont ON g.content_id = cont.gdskgcrefid
               LEFT OUTER JOIN webset.disdef_bgb_measure measur ON g.meas_id = measur.mrefid
               LEFT OUTER JOIN webset.disdef_bgb_ksaeval sched ON g.sched_id = sched.erefid
         WHERE stdrefid = " . $tsRefID . "
           AND iepyear = $stdIEPYear
           AND esy = '" . $esy .  "'
         ORDER BY COALESCE(order_num, grefid)
         ";

    if ($esy == "Y") {
    	$list->title .= "ESY ";
    }

	$list->addColumn("Order #", "5%");
	$list->addColumn("Area", "10%");
    $list->addColumn("Annual Goal", "40%");
    $list->addColumn("Evaluation Procedure");
    $list->addColumn("View Objectives", "15%")
    	 ->type('link')
    	 ->param('javascript:objectives(AF_REFID);')
    	 ->dataCallback('objCallBack');
    
    $list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.std_oth_goals')
				->setKeyField('grefid')
				->applyListClassMode()
	);
	
	$list->addButton(
		FFButton::factory() 
			->value('Goal Bank') 
			->onClick('goalBank()')
	);

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );
    
    $list->printList();
    
    print FFInput::factory()->name('esy')->value($esy)->hide()->toHTML();
    print FFInput::factory()->name('dskey')->value($dskey)->hide()->toHTML();

    function objCallBack($value) {
		return "Objectives ($value[5])";
    }
    
?>

<script type="text/javascript">
	
    function objectives(grefID) {
        url = api.url('oth_objectives_list.php', {'grefid': grefID, 'ESY': $('#esy').val(), 'dskey': $('#dskey').val()});
		api.window.open('Objectives', url)
			.addEventListener(
				WindowEvent.CLOSE,
				function(e) {
					ListClass.get().reload();
				}
			); 
    }
    
    function goalBank() {
		
		url = api.url('oth_bank_main.php', {'ESY': $('#esy').val(), 'dskey': $('#dskey').val()});
		api.window.open('Goal Bank', url)
			.addEventListener(
				WindowEvent.CLOSE,
				function(e) {
					ListClass.get().reload();
				}
			); 
		
    }
    
</script>