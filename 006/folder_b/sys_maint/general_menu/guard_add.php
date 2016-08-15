<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->title = "Add/Edit Guardian Type ";

	$edit->setSourceTable('webset.def_guardiantype', 'gtrefid');

	$edit->addGroup("General Information");

	$edit->addControl("Desc")
		->sqlField('gtdesc')
		->req();

	$edit->addControl("Rank", "int")
		->sqlField('gtrank');

	$edit->addControl("Deactivation Date", "date")
		->sqlField('enddate');

	$edit->addControl(FFSelect::factory("State Code"))
		->sqlField('sc_refid')
		->sql("
			SELECT NULL::INTEGER, '--Not Defined--'
			       UNION ALL
              (SELECT sc_refid, sc_statecode || ' - ' || sc_name
			     FROM c_manager_statedef.sc_relationship_to_student
			    WHERE TRIM(sc_statecode) = (SELECT vndstate
                                               FROM public.sys_vndmst
                                              WHERE vndrefid = " . SystemCore::$VndRefID . ")
			    ORDER BY sc_rank)
		");

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('guard_list.php');
	$edit->cancelURL = CoreUtils::getURL('guard_list.php');

	$edit->printEdit();
?>
