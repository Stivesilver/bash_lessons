<?php
	Security::init();

	$values = db::execSQL("
		SELECT set_refid,
			   mitemapp,
			   mitemgroup
          FROM webset.sped_menu MM
         ORDER BY mrefid DESC
         LIMIT 1
	")->assocAll();
	$values = $values[0];

	$RefID = io::geti('RefID');
	$IEPFormat = io::get('IEPFormat');

	$edit = new editClass("edit1", $RefID);

	$edit->setSourceTable('webset.sped_menu', 'mrefid');

	$edit->title = "Add/Edit State Menu Item";

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("IEP Format"))
		->sql("
			SELECT srefid,
                   shortdesc
              FROM webset.sped_menu_set
             ORDER BY state, shortdesc
		")
		->sqlField('set_refid')
		->value($values['set_refid'])
		->req();

	$edit->addControl(FFSelect::factory("Application"))
		->sql("
		    SELECT scr_refid,
			       scr_name
			  FROM webset.sped_screen
			 ORDER BY scr_refid
		")
		->sqlField('mitemapp')
		->value($values['mitemapp'])
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

	$names = db::execSQL("
		SELECT DISTINCT mitemgroup, mitemgroup
          FROM webset.sped_menu MM
               LEFT OUTER JOIN webset.sped_menudef MD ON MM.mdrefid=MD.mdrefid
               INNER JOIN webset.sped_menu_set ON webset.sped_menu_set.srefid = MM.set_refid
         WHERE sped_menu_set.srefid = $IEPFormat
         ORDER BY mitemgroup
		")->assocAll();
	$groupHelp = '';
	foreach ($names as $name) {
		$groupHelp .= $name['mitemgroup'] . "<br/>";
	}

	$edit->addControl(FFInputDropList::factory("Group"))
		->dropListSQL("
			SELECT DISTINCT mitemgroup, mitemgroup
	          FROM webset.sped_menu MM
	               LEFT OUTER JOIN webset.sped_menudef MD ON MM.mdrefid=MD.mdrefid
	               INNER JOIN webset.sped_menu_set ON webset.sped_menu_set.srefid = MM.set_refid
	         WHERE sped_menu_set.srefid = $IEPFormat
	         ORDER BY mitemgroup
		")
		->highlightField(false)
		->sqlField('mitemgroup')
		->value($values['mitemgroup'])
		->width('400px')
		->help($groupHelp)
		->req();

	$edit->addControl(FFSwitchYN::factory("Item New Line"))
		->sqlField('mitemnewline')
		->req();

	$edit->addControl(FFSwitchYN::factory("Group New Line"))
		->sqlField('mgroupnewline')
		->req();

	$edit->addControl(FFSwitchYN::factory("IEP Year Req"))
		->sqlField('mitem_iep_req_sw')
		->req();

	$edit->addControl("Condition")->sqlField('displcondition')->width(300);

	$edit->addControl("Check Method")->sqlField('check_method')->width(300);
	$edit->addControl("Check Parameter")->sqlField('check_param')->width(300);

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./menu_man_list.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./menu_man_list.php', array('staterefid' => -1));

	$edit->printEdit();
?>
