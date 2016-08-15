<?php
   
    Security::init();
   
   	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
    $area		= io::get('area');
    $RefID  	= io::geti('RefID');
    $edit 	    = new editClass('edit1', $RefID);
    $goal 	    = new IDEAGoalDBHelper($area); 
    
    $edit->title = 'Add/Edit ' . $goal->getAttr('type');   
    
    /* because we use area */
    $goal->checkKsaCMColumn();
        
	$edit->SQL = $goal->getQueryItemEdit($RefID);
	
	$edit->setSourceTable($goal->getAttr('table'), $goal->getRifID());
	$edit->addGroup("General Information");

    $edit->addControl($edit->title, "EDIT")
    	 ->sqlField($goal->getColumn('item')) 
    	 ->size(60);
    	 
    $edit->addControl("Deactivation Date", "DATE")
    	 ->sqlField('enddate');  
    	 
    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "PROTECTED")
    	 ->sqlField('lastuser')	
    	 ->value($_SESSION["s_userUID"]);
    	 
	$edit->addControl("Last Update", "PROTECTED")
		 ->sqlField('lastupdate')	
		 ->value(date("m-d-Y H:i:s"));
		 
    if (io::geti('area_id') > 0) {
		$edit->addControl("", "HIDDEN")
			 ->sqlField($goal->getColumn('ksaID'))
			 ->value(io::geti('area_id'));
    } 
    
    $edit->addControl("", "HIDDEN")
    	 ->sqlField('umrefid')
    	 ->value($_SESSION["s_userID"]);
    
	$edit->printEdit();
	
?>
