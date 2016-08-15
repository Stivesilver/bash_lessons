<?php
   
    Security::init();
   
   	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
    $area		= io::get('area');
    $list 		= new ListClass();
	$goal       = new IDEAGoalDBHelper($area);
		
	$goal->addAriaID(io::get('area_id'));
    
    $editUrl = CoreUtils::getURL(
	  	 		   'oth_items_edit.php', 
	  	 		   array(
	  	 		  	   'dskey'      => $dskey, 
	  	 		  	   'area'       => $area,
	  	 		  	   'area_id'	=> io::geti('area_id')
	  	 		  	   )
	  	 		  );
	  	 		  
	$processUrl = CoreUtils::getURL(
	  	 		   'oth_items_dactive.ajax.php', 
	  	 		   array(
	  	 		  	   'dskey'      => $dskey, 
	  	 		  	   'area'       => $area,
	  	 		  	   'area_id'	=> io::geti('area_id')
	  	 		  	   )
	  	 		  );  	 		  
    
    $list->addURL           = $editUrl;
    $list->editURL          = $editUrl;
	$list->showSearchFields = true;
	$list->title 	        = $goal->getAttr('type');
	$list->SQL 			    = $goal->getQueryItemList();

	$list->addSearchField('Status', '', 'list')
		->value('1')
		->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
		->data(
			array(
				1 => 'Active',
				2 => 'Inactive'
			)
		);

	$list->addColumn($list->title);
		
	$list->addColumn("Status");

	$list->getButton(ListClassButton::PROCESS)
		 ->value("Dactivate");
	
	$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($goal->getAttr('table'))
				->setKeyField($goal->getRifID())
				->applyListClassMode()
	);
	
	$list->addRecordsProcess('Deactivate')
		->message('Do you really want to deactivate records?')
		->url(CoreUtils::getURL($processUrl))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);

	$list->printList();
	
?>
