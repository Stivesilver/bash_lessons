<?php

	Security::init();

    $dskey    = io::get('dskey');
	$ds 	  = DataStorage::factory($dskey, true);
	$tsRefID  = $ds->safeGet('tsRefID'); 
	$areaID   = 108;
    $edit 	  = new EditClass("edit1", $tsRefID);
	
	$edit->setSourceTable('webset.std_general', 'refid');

    $edit->title = "Documentation of English Language Proficiency when the Student is an English Learner (EL)";

	$edit->addGroup("General Information");
	$edit->addControl("Date", "date")
		 ->sqlField('dat01');
        
	$edit->addControl("Assessment/Documentation", "edit")
		->sqlField('txt01')
		->size(80);

	$edit->addControl("Result/Score", "edit")
		 ->sqlField('txt02')
		 ->size(30);
        
	$edit->addControl("Sequence", "integer")
		 ->sqlField('order_num')
		 ->value(
		 	db::execSQL("
    	 	  	SELECT max(order_num)
	          	  FROM webset.std_general
	         	 WHERE stdrefid = " . $tsRefID . "
               	   AND area_id = $areaID
    	 	  ")->getOne() + 1
    	 	  );
        
	$edit->addGroup("Update Information");
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
