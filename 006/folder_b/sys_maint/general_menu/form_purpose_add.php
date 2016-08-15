<?php

	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass("edit1", $RefID);

	$edit->title = "Add/Edit Menu Definition";

	$edit->setSourceTable('webset.def_formpurpose', 'mfcprefid');

	$edit->addGroup('General Information');

	$edit->addControl("Form Purpose")->width(400)->sqlField('mfcpdesc');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./menuDef.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./menuDef.php', array('staterefid' => -1));

	$edit->printEdit();

	if ($RefID > 0) {

		$list = new listClass();

		$list->title = "Attached Forms";

		$list->SQL = "
			SELECT mfcrefid,
                   mfcpdesc,
                   state,
                   mfcdoctitle,
                   mfcfilename,
                   CASE WHEN NOW() > stf.recdeactivationdt   THEN 'In-Active' ELSE 'Active' END  as status
              FROM webset.statedef_forms stf
                   INNER JOIN webset.def_formpurpose ON  webset.def_formpurpose.mfcprefid  = stf.mfcprefid
                   INNER JOIN webset.glb_statemst ON  webset.glb_statemst.staterefid = stf.screfid
             WHERE stf.mfcprefid = $RefID
             ORDER BY state, mfcpdesc, mfcdoctitle
	    ";

		$list->showSearchFields = "yes";
		$list->addSearchField("ID", "(mfcrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");

		$list->addColumn('ID')->sqlField('mfcrefid');
		$list->addColumn("Form Purpose")->sqlField('mfcpdesc')->type('group');
		$list->addColumn("State")->sqlField('state');
		$list->addColumn("Form Title")->sqlField('mfcdoctitle');
		$list->addColumn("&nbsp")->sqlField('mfcfilename');
		$list->addColumn("Form Status")->sqlField('status');

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.statedef_forms')
				->setKeyField('mfcrefid')
				->applyListClassMode()
		);

		print "<table><form></table></form>";
		$list->printList();
	}

?>
