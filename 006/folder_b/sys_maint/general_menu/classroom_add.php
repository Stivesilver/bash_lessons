<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.def_classroomtype', 'crtrefid');

	$edit->title = "Add/Edit Classroom Type ";

	$edit->addGroup("General Information");

	$edit->addControl("Classroom Type")->sqlField('crtdesc');
	$edit->addControl(FFSelect::factory("Narrative"))
		->sql("
			SELECT validvalueid, validvalue
              FROM webset.glb_validvalues
		     WHERE (valuename = 'YesNo')
             ORDER BY validvalueid
		")
		->sqlField('crtnarrsw');

	$edit->addGroup("Record Status Information");

	$edit->addControl("Record Activation Date", "date")->sqlField('recactivationdt');
	$edit->addControl("Record Deactivation Date", "date")->sqlField('recdeactivationdt');
	$edit->addControl(FFSelect::factory("Scheduled Record Status"))
		->sql("SELECT srsdrefid,
                 CAST(srsdsw AS VARCHAR)||' - '||srsddesc,
                 CASE srsdsw WHEN 'R' THEN ' ' ELSE srsdsw END AS order_column
            FROM webset.def_systemrecordstatus
           ORDER BY order_column
		")
		->sqlField('recstatusrefid');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./classroom_list.php');
	$edit->cancelURL = CoreUtils::getURL('./classroom_list.php');

	$edit->printEdit();
?>
