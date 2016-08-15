<?php

	Security::init();

  	$dskey   = io::get('dskey');
	$ds 	 = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID'); 
	$refID 	 = io::geti('RefID'); 
	$areaID  = 101;
	$edit    = new editClass("edit1", $refID);

	$edit->setSourceTable('webset.std_general', 'refid');
	
    $edit->title = "Core Instruction Provided";

	$edit->addGroup("General Information");
    	 	  
	$edit->addControl("Academic Area", "select")
		->sqlField('int01')
		->sql("
			SELECT refid,
                   validvalue
	          FROM webset.disdef_validvalues
             WHERE vndrefid = " . $_SESSION["s_VndRefID"] . "
	           AND valuename = 'ID_SLD_Concern_Area'
	           AND (glb_enddate IS NULL or now()< glb_enddate)
	         ORDER BY sequence_number, validvalue ASC
	         ");
        
	$edit->addControl("Core Instruction", "edit")
		->sqlField('txt01')
		->size(80)
		->req(true);
        
	$edit->addControl("Begin Date", "date")
		 ->sqlField('dat01');
        
	$edit->addControl("End Date", "date")
		 ->sqlField('dat02');
        
	$edit->addControl("Total (weeks)", "integer")
		 ->sqlField('int02')
		 ->size(30);
        
	$edit->addControl("Frequency (how often per week)", "edit")
		 ->sqlField('txt02')
		 ->size(80);
        
	$edit->addControl("Intensity (minutes per session)", "edit")
	     ->sqlField('txt03')
	     ->size(80);
        
	$edit->addControl("Sequence", "integer")
	     ->sqlField('order_num')
	     ->value(db::execSQL("
	     	SELECT max(order_num)
	              FROM webset.std_general
	             WHERE stdrefid = ". $tsRefID . "
                   AND area_id = $areaID
	     ")->getOne() + 1);
        
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
