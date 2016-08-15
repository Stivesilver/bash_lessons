<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.sped_importmst', 'imrefid');

	$edit->title = "Add/Edit Import Area";

	$edit->SQL = "SELECT setrefid,
                           imarea,
                           seqnum,
                           enddate,
                           lastuser,
                           lastupdate
					  FROM webset.sped_importmst
					 WHERE imrefid=$RefID";

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("IEP Type"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY shortdesc
		")
		->sqlField('setrefid');
	$edit->addControl("Name")->sqlField('imarea');
	$edit->addControl("Display Sequence", "int")->sqlField('seqnum');;
	$edit->addControl("Deactivation Date", "date")->sqlField('enddate');;
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./importareas_list.php');
	$edit->cancelURL = CoreUtils::getURL('./importareas_list.php');

	$edit->printEdit();
?>
