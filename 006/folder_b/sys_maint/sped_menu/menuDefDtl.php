<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass("edit1", $RefID);

	$edit->title = "Add/Edit State Menu Item";

	$edit->setSourceTable('webset.sped_menu', 'mrefid');

	$edit->addGroup('General Information');

	if (io::get('IEPFormat')) {
		$edit->addControl("IEP Format", "HIDDEN")->value(io::get('IEPFormat'))->sqlField('set_refid')->req();
	} else {
		$edit->addControl(FFSelect::factory("IEP Format"))
			->sql("
				SELECT srefid, state || ' - ' || shortdesc
                  FROM webset.sped_menu_set
                 ORDER BY state, shortdesc
	        ")
			->sqlField('set_refid')
			->req();
	}

	$edit->addControl(FFSelect::factory("Application"))
		->sql("
			SELECT 1, 'Sp Ed Student Mgr'
             UNION ALL
            SELECT 2, 'Sp Ed Progress Report Mgr'
		")
		->sqlField('mitemapp')
		->req();

	$edit->addControl(FFSelect::factory("Menu Item"))
		->sql("
			SELECT -1 AS mdrefid, '-- None' AS mdname
			 UNION ALL SELECT mdrefid, mdname
			  FROM webset.sped_menudef
			 ORDER BY mdname
		")
		->sqlField('mdrefid')
		->req();

	$edit->addGroup('Sorting Information');

	$edit->addControl("Item Order", "int")
		->sqlField('mitemorder')
		->req();

	$edit->addControl("Group", "text")
		->sqlField('mitemgroup')
		->req();

	$edit->addControl(FFSwitchYN::factory("Item New Line"))
		->sqlField('mitemnewline')
		->req();

	$edit->addControl(FFSwitchYN::factory("Group New Line"))
		->sqlField('mgroupnewline')
		->req();

	$edit->addControl("Condition")->sqlField('displcondition');

	$edit->addControl("Check Method")->sqlField('check_method');
	$edit->addControl("Check Parameter")->sqlField('check_param');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./menuDefAdd.php', array('RefID' => io::get('mdrefid')));
	$edit->cancelURL = CoreUtils::getURL('./menuDefAdd.php', array('RefID' => io::get('mdrefid')));

	$edit->printEdit();
?>
