<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Grade Level of Performance';

	$edit->setSourceTable('webset.std_in_pglp', 'pglprefid');

	if (IDEACore::disParam(15) == 'Y') {

		$gradeSQL = "
			SELECT glrefid, 
			       gldesc
		      FROM webset.disdef_gradelevel
		     WHERE vndrefid = VNDREFID
		     ORDER BY CASE lower(gldesc)!=gldesc WHEN true then '00' || length(gldesc) ELSE gldesc END
		";
	} else {

		$gradeSQL = "
			SELECT gl_refid, 
			       gl_code
			  FROM c_manager.def_grade_levels
		     WHERE vndrefid = VNDREFID
		     ORDER BY gl_numeric_value
		";
	}

	$edit->addGroup('General Information');
	$edit->addControl('Letter Grade', 'edit')->sqlField('pglplgrade');

	$edit->addControl('Grade Equivalent', 'select')
		->sqlField('glrefid')
		->sql($gradeSQL)
		->emptyOption(true)
		->req();

	$course_title = IDEACore::disParam(118);
	$course_title = $course_title != '' ? $course_title : 'Course';

	$edit->addControl($course_title, 'select')
		->sqlField('tsnrefid')
		->sql("
			SELECT tsnrefid, 
			       CASE WHEN tsnnum IS NULL THEN '' ELSE '#' || tsnnum || ' - ' END || tsndesc
		   	  FROM webset.disdef_tsn
	         WHERE vndrefid = VNDREFID
	           AND recdeactivationdt IS NULL or now()< recdeactivationdt
	         ORDER BY tsnnum
		")
		->emptyOption(true)
		->req()
	;

	$edit->addControl('Narrative', 'textarea')
		->sqlField('pglpnarrative')
		->css("width", "100%")
		->css("height", "150px");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('pglp_level.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('pglp_level.php', array('dskey' => $dskey));

	$edit->printEdit();
?>