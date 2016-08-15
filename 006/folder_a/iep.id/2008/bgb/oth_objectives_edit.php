<?php

	Security::init();

	$dskey   	= io::get('dskey');
	$RefID		= io::get('RefID');
	$ds 	 	= DataStorage::factory($dskey, true);
	$tsRefID 	= $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
    $SQL 		= "
    	SELECT *
          FROM webset.std_oth_goals
         WHERE grefid=" . io::get("grefid")
        ;
             
    $rs_goal = db::execSQL($SQL);
    $SQL     = "
    	SELECT max(order_num)
          FROM webset.std_oth_objectives
         WHERE grefid=" . io::get("grefid")
        ;

    $order_num = db::execSQL($SQL)->getOne() + 1;
    $edit 	   = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset.std_oth_objectives', 'orefid');

    $edit->title = "Add/Edit Objectives";

	$edit->addGroup("General Information");
	$edit->addControl("Order #", "integer")
		->value($order_num)
		->sqlField('order_num')
		->size(4);

	$edit->addControl("7. Objective (required if student takes the IAA):", "textarea")
		->sqlField('objective_own')
		->css("width", "100%")
		->css("height", "100px")
		->req(true)
		->autoHeight(true);

	$edit->addGroup("Progress Information");
	$edit->addControl("Expected Progress", "edit")
		 ->sqlField('progress')
		 ->size(80);
    
	$edit->addControl("Target Date", "date")
		 ->sqlField('target_dt');

	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")
		 ->value($_SESSION["s_userUID"])
		 ->sqlField('lastuser');

	$edit->addControl("Last Update", "protected")
		 ->value(date("m-d-Y H:i:s"))
		 ->sqlField('lastupdate');

	$edit->addControl("Goal ID", "hidden")
		 ->value(io::get("grefid"))
		 ->sqlField('grefid');

	/*$edit->getButton(EditClassButton::SAVE_AND_ADD)
		 ->value("");*/

    $edit->printEdit();

?>
