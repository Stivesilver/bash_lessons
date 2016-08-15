<?php
  	
  	Security::init();

    $dskey    = io::get('dskey');
	$ds 	  = DataStorage::factory($dskey, true);
	$tsRefID  = $ds->safeGet('tsRefID'); 
	$areaID   = 107;
	$refID 	  = (int)db::execSQL("
    	 	  	    SELECT refid
                      FROM webset.std_general
                     WHERE stdrefid = " . $tsRefID . "
                       AND area_id = $areaID
    	 	    ")->getOne();

	$edit = new EditClass("edit1", $refID);

	$edit->setSourceTable('webset.std_general', 'refid');
	
    $edit->saveAndEdit = true;
    $edit->title 	   = "English Learner (EL)";

	$edit->addGroup("General Information");
	$edit->addControl("Is the student's first language English?", "select_radio")
		->sqlField('txt01')
        ->data(
    	 	array(
				'Y' => 'Yes', 
				'N' => 'No'
    	 	)
    	 );           
    
	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")
		 ->value($_SESSION["s_userUID"])
		 ->sqlField('lastuser');
    
	$edit->addControl("Last Update", "protected")
		 ->value(date("m-d-Y H:i:s"))
		 ->sqlField('lastupdate');
    
	$edit->addControl("", "hidden")
		 ->value($tsRefID)
		 ->sqlField('stdrefid');
    
	$edit->addControl("", "hidden")
		 ->value($areaID)
		 ->sqlField('area_id');

    $edit->firstCellWidth = "30%";

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

    $edit->printEdit();

?>
