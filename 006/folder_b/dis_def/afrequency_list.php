<?php

      Security::init();

      $refId = io::geti('RefID');
       
	  $list = new listClass();

	  $list->title            = "Frequency";
	  $list->addURL 	      = "afrequency_edit.php";
	  $list->editURL	      = "afrequency_edit.php";
	  $list->multipleEdit     = "yes";
	  $list->customSearch     = "yes";
	  $list->showSearchFields = "yes";
                    
	  $list->SQL = "
	  	  SELECT sfrefid,
                 sfdesc,
                 seqnum,
                 CASE WHEN NOW() > enddate  THEN 'In-Active' ELSE 'Active' END  as status
		    FROM webset.def_spedfreq
		   WHERE vndrefid = VNDREFID ADD_SEARCH
        ORDER BY seqnum, sfdesc
        ";

      $list->addSearchField('Status', '', 'List')
        	->value('1')
        	->data(array(1 => "Active", 2 => "Inactive"))
        	->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)');
        	
      $list->addSearchField("Frequency", "sfdesc");
	  $list->addColumn("Frequency", "", "text", "", "", "");
	  $list->addColumn("Sequence", "", "text", "", "", "");
      $list->addColumn("Current Record Status", "", "text", "", "", "");
   		
   	  $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.def_spedfreq')
              ->setKeyField('sfrefid')
              ->applyListClassMode()
      );  
        
	  $list->printList();
		
?>