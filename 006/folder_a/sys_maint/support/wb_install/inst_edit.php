<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->title = "Add/Edit Data Mode";

	$edit->setSourceTable('webset.sys_install', 'refid');

	$edit->addGroup("General Information");

	$edit->addControl("Table", "protected")->sqlField('webset_table');

	$edit->addControl(FFRadioList::factory('Data Mode'))
		->sql("
			SELECT 'D',
			       'Include Data'
			 UNION
			SELECT 'S',
			       'Structure ONLY'
			 UNION
			SELECT 'N',
			       'TABLE NOT needed'
       ")
		->sqlField('datamode')
		->value('S');

	$edit->addControl("State", "select_check")
		->sql("
			SELECT staterefid,
			       state
			  FROM webset.sped_sm_area
			       LEFT JOIN webset.glb_statemst ON glb_statemst.staterefid = screfid
			 WHERE screfid > 0
			 GROUP BY staterefid, state
		")
		->sqlField('states')
		->breakRow();

	$edit->addControl(FFSwitchYN::factory('Export as psql'))
		->sqlField('psql');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('inst_list.php');
	$edit->cancelURL = CoreUtils::getURL('inst_list.php');

	$edit->printEdit();
?>
