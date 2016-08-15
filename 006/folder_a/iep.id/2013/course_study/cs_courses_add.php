<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$area_id = 136;

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Courses of Study';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->finishURL = CoreUtils::getURL('cs_courses.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('cs_courses.php', array('dskey' => $dskey));

	$edit->addGroup('General Information');

	$edit->addControl('School Year', 'select')
		->sqlField('int01')
		->name('int01')
		->sql("
			SELECT dsyrefid, dsydesc
			  FROM webset.disdef_schoolyear
			 WHERE vndrefid = VNDREFID
			 ORDER BY dsybgdt ASC
		")
		->emptyOption(TRUE)
		->req();
	
	$edit->addControl('Status', 'select')
		->sqlField('int02')
		->name('int02')
		->sql("
			SELECT refid, 
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_Course_Study_Grade'
			   AND (glb_enddate IS NULL or now()< glb_enddate)			   
		     ORDER BY sequence_number, validvalue
		")
		->emptyOption(TRUE)
		->req();
	
	$edit->addControl('Courses', 'textarea')
		->sqlField('txt01')
		->css('width', '100%')
		->css('height', '150px')
		->autoHeight(true);

	$edit->addControl('Credits Earned', 'textarea')
		->sqlField('txt02')
		->css('width', '100%')
		->css('height', '50px')
		->autoHeight(true);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');

	$edit->printEdit();
?>
