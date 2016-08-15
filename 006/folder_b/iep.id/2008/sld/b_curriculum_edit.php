<?php

	Security::init();

  	$dskey    = io::get('dskey');
	$refID 	  = io::geti('RefID'); 
	$ds 	  = DataStorage::factory($dskey, true);
	$tsRefID  = $ds->safeGet('tsRefID'); 
	$areaID   = 100;
	$edit     = new EditClass("edit1", $refID);
	$orderNum = db::execSQL("
    	 	  	  SELECT MAX(order_num) FROM webset.std_general
    	 	  	   WHERE stdrefid = " . $tsRefID . "
    	 	  ")->getOne();  
	
	$edit->title = "Data that establishes that the core curriculum is effective for most students.";
    
	$edit->setSourceTable('webset.std_general', 'refid'); 
    $edit->addGroup("General Information");
    
    $edit->addControl("Order #", "integer")
		 ->sqlField('order_num')
		 ->size(4)
		 ->value(db::execSQL("
    	 	  	  SELECT MAX(order_num) FROM webset.std_general
    	 	  	   WHERE stdrefid = " . $tsRefID . "
    	 	  	     AND area_id = $areaID
    	 	  ")->getOne() + 1);
                              	
	$edit->addControl("Name of Assessment", "edit")
		->sqlField('txt01')
		->size(80)
		->req(true);
        
	$edit->addControl("Area Assessed", "select")
		->sqlField('int01')
		->sql("SELECT aaarefid, aaadesc
	       	     FROM webset.statedef_assess_acc
	    		WHERE screfid = 14
                  AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	            ORDER BY 2");
        
	$edit->addControl("Date", "date")
		 ->sqlField('dat01');
        
	$edit->addControl("Performance Benchmark", "edit")
		 ->sqlField('txt02')
		 ->size(80);
        
	$edit->addControl("Percentage of Grade Level Peers Meeting Performance Benchmark", "edit")
		 ->sqlField('txt03')
		 ->size(50);
        
	$edit->addControl("Percentage of Disaggregated Group Level Peers Meeting Performance Benchmark (if applicable)", "edit")
		 ->sqlField('txt04')
		 ->size(50);
        
	$edit->addControl("Target Student Performance Level", "textarea")
		->sqlField('txt05')
		->css("width", "100%")
		->css("height", "80")
		->autoHeight(true);
        
	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")
		 ->value($_SESSION["s_userUID"])
		 ->sqlField('lastuser');
	
	$edit->addControl("Last Update", "protected")
		 ->value(date("m-d-Y H:i:s"))
		 ->sqlField('lastupdate');
		
	$edit->addControl("tsRefID", "hidden")
		 ->value($tsRefID)
		 ->sqlField('stdrefid');
		
	$edit->addControl("area_id", "hidden")
		 ->value($areaID)
		 ->sqlField('area_id');
		 
    $edit->firstCellWidth = "35%";

	$edit->printEdit();

?>
