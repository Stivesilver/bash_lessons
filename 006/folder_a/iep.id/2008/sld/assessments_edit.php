<?php
  
  	Security::init();
  	
  	$dskey   = io::get('dskey');
	$ds 	 = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID'); 
	$refID 	 = io::geti('RefID'); 
	$areaID  = io::geti('area_id');  
	$edit    = new editClass("edit1", $refID);
  
	$edit->setSourceTable('webset.std_general', 'refid');

    $edit->title = 'Pattern of Strengths and Weaknesses in Psychological Processing Skills That Impact Learning';

	$edit->addGroup("General Information");
	$edit->addControl("Order #", "integer")
		 ->sqlField('order_num')
		 ->size(4)
		 ->value(db::execSQL("
    	 	  		  SELECT max(order_num)
		                FROM webset.std_general
		               WHERE stdrefid = " . $tsRefID . "
	                     AND area_id = " . $areaID
    	 	  	 )->getOne() + 1);
		 
	$edit->addControl('Area of Concern', "textarea")
		->sqlField('txt08')
		->css("WIDTH", "70%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
		
	$edit->addControl("Date", "date")
		 ->sqlField('dat01');

	$edit->addControl("Name of Assessment", "edit")
		->sqlField('txt01')
		->size(80);
		
	$edit->addControl("Subtest(s)", "textarea")
		->sqlField('txt02')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
        
	$edit->addControl("SS", "textarea")
		->sqlField('txt03')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
        
	$edit->addControl("%ile", "textarea")
		->sqlField('txt04')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
        
	$edit->addControl("Evaluator/Title", "edit")
		->sqlField('txt05')
		->size(50);
		
	$edit->addControl("Description of assessment measure, validity statement, and interpretive information", "textarea")
		->sqlField('txt06')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
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

    $edit->firstCellWidth = "25%";

	$edit->printEdit();
  
?>
