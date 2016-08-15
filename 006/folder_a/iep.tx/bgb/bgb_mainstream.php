<?php

	Security::init();

	$dskey = io::get('dskey');
	$esy = io::get('ESY');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);

	$prevGoal = db::execSQL("
		SELECT *
          FROM webset_tx.std_sb_goals
		 WHERE stdrefid = " . $tsRefID . "
	     ORDER BY grefid DESC
		 LIMIT 1
	")->assoc();

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Mainstream';
	$edit->topButtons = TRUE;
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset_tx.std_sam_general', 'iepyear');

	$edit->addGroup('General Information');
	$edit->addControl('Mainstream', 'textarea')
		->sqlField('mainstream_taks')
		->name('mainstream_taks')
		->css('width', '100%')
		->css('height', '150px')
		->append(
			FFButton::factory('Default answer')
				->onClick('$("#mainstream_taks").val("Student will make measurable progress toward grade level TEKS: " + $.trim($("#mainstream_taks").val()))')
				->toHTML()
	);

	$edit->addGroup('Services Information');
	$edit->addControl('Service Type', 'select')
		->sqlField('servtype')
		->name('servtype')
		->value($prevGoal['servtype'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceType'
			 ORDER BY refid
		");

	$edit->addControl('Service Start Date', 'date')
		->sqlField('durbeg')
		->name('durbeg')
		->value($student->getDate('stdenrolldt'));

	$edit->addControl('Service End Date', 'date')
		->sqlField('durend')
		->name('durend')
		->value($student->getDate('stdcmpltdt'));

	$edit->addControl('Location', 'select')
		->sqlField('location')
		->name('location')
		->value($prevGoal['location'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceLoc'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Specify Location')
		->sqlField('locationoth')
		->name('locationoth')
		->value($prevGoal['locationoth'])
		->showIf('location', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceLoc'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Implementors', 'select')
		->sqlField('implement')
		->name('implement')
		->value($prevGoal['implement'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceLoc'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Specify Implementors')
		->sqlField('implementoth')
		->name('implementoth')
		->value($prevGoal['implementoth'])
		->showIf('implement', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceLoc'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup('Progress Reporting');
	$edit->addControl('Schedule For Evaluation', 'select')
		->sqlField('schedule')
		->name('schedule')
		->value($prevGoal['schedule'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceSchedule'
			 ORDER BY refid
        ");

	$edit->addControl('Specify Schedule')
		->sqlField('scheduleoth')
		->name('scheduleoth')
		->value($prevGoal['scheduleoth'])
		->showIf('schedule', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceSchedule'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Notify of progress by', 'select_check')
		->sqlField('notice')
		->name('notice')
		->value($prevGoal['notice'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceNotice'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Other Method')
		->sqlField('noticeoth')
		->name('noticeoth')
		->value($prevGoal['noticeoth'])
		->size(50);

	$edit->addGroup('Evaluation Information');
	$edit->addControl('Level of Mastery Criteria')
		->sqlField('level')
		->name('level')
		->size(50);

	$edit->addControl('Evaluation Procedures', 'select_check')
		->sqlField('evalproc')
		->name('evalproc')
		->value($prevGoal['evalproc'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXbgbEval'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Other Procedure')
		->sqlField('evalprocoth')
		->name('evalprocoth')
		->value($prevGoal['evalprocoth'])
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = 'javascript:parent.switchTab();';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_other')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>