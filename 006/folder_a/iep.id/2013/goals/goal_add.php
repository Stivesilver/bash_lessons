<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = 142;

	$previous = db::execSQL("
	    SELECT *
		  FROM webset.std_general std
	      WHERE stdrefid = " . $tsRefID . "
		    AND iepyear = " . $stdIEPYear . "
		    AND area_id = " . $area_id . "
	 	  ORDER BY 1 DESC
	")->assoc();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Annual Goals';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addTab('General Information');

	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
				SELECT max(order_num)
				  FROM webset.std_general std
				 WHERE stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
				   AND area_id = " . $area_id . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Skill Area', 'select')
		->sqlField('int01')
		->name('int01')
		->sql("
			SELECT gdskrefid,
				   " . IDEAParts::get('baselineArea') . "
			  FROM webset.disdef_bgb_goaldomainscopeksa ksa
				   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
				   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
			 WHERE domain.vndrefid = VNDREFID
			   AND (CASE ksa.enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
			 ORDER BY domain.gdsdesc, scope.gdssdesc, gdsksdesc
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Present Level of Performance', 'textarea')
		->sqlField('txt01')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('How does the student\'s disability affect his or her involvement in and progress in the general education curriculum? List the student\'s current level (baseline data) and the assessment where the data was obtained. (State how the goal links to the postsecondary goal).');

	$edit->addControl('General Education Content Standard(s)', 'textarea')
		->sqlField('txt02')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Idaho Content Standards, Idaho Core, Idaho Work Place Competencies, Idaho Extended Content Standards');

	$edit->addControl('Annual Goal', 'textarea')
		->sqlField('txt03')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Must list the condition or level of instruction, the behavior or skill, and the criteria (must be aligned to baseline data identified in the Present Level of Performance)')
		->req();

	$edit->addControl('Evaluation Procedure', 'select_check')
		->sqlField('txt04')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_Eval_Procedure'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
		     ORDER BY sequence_number, validvalue
		")
		->value($previous['txt04'])
		->displaySelectAllButton(false)
		->breakRow();

	$edit->addControl('Assessment Name')
		->sqlField('txt05')
		->size(50);

	$edit->addControl('Schedule of Data Collection', 'select_check')
		->sqlField('txt06')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_Schedule_DC'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
		     ORDER BY sequence_number, validvalue
		")
		->value($previous['txt06'])
		->displaySelectAllButton(false)
		->breakRow();

	$edit->addControl('Specify if Other')
		->sqlField('txt07')
		->value($previous['txt07'])
		->size(50);

	$edit->addControl('Assistive Technology (if needed)', 'textarea')
		->sqlField('txt08')
		->value($previous['txt08'])
		->css('width', '100%')
		->css('height', '50px')
		->autoHeight(true);

	$edit->addControl('How/when progress will be reported to the family: Enter report card dates in the 1st line below', 'textarea')
		->sqlField('txt09')
		->value($previous['txt09'])
		->css('width', '100%')
		->css('height', '50px')
		->autoHeight(true);

	$edit->addTab('Objectives');
	$edit->addIFrame(CoreUtils::getURL('objectives.php', array('dskey' => $dskey, 'goal' => $RefID)))->height('300');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');

	$edit->finishURL = CoreUtils::getURL('goal_main.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('goal_main.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
