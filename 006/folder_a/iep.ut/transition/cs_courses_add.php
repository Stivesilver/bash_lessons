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
	
	$edit->addControl('Grade', 'select')
		->sqlField('int02')
		->name('int02')
		->sql("
			SELECT gl_refid,
				   gl_code
			  FROM c_manager.def_grade_levels
			 WHERE vndrefid = VNDREFID
			   AND gl_code IN ('9','10','11','12')
			 ORDER BY gl_numeric_value
		")
		->emptyOption(TRUE)
		->req();
	
	$edit->addControl('Courses', 'select')
		->sqlField('int03')
		->name('int03')
		->sql("
			SELECT tsscrefid,
				   tsscdesc
			  FROM webset.statedef_ts_studycourse crs
				   LEFT OUTER JOIN webset.sped_menu_set iep ON iep.srefid = set_id
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY CASE WHEN SUBSTRING(LOWER(tsscdesc), 1, 5) = 'other' THEN 2 ELSE 1 END, 2
		");

	$edit->addControl('Specify')
		->sqlField('txt01')
		->css('width', '100%')
		->showIf('int03', db::execSQL("
							  SELECT tsscrefid
								FROM webset.statedef_ts_studycourse
							   WHERE SUBSTRING(LOWER(tsscdesc), 1, 5) = 'other'
							 ")->indexAll());


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');

	$edit->printEdit();
?>
