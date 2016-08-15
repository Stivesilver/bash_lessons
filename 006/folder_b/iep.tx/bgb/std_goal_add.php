<?php
	Security::init();

	$dskey = io::get('dskey');
	$esy = io::get('ESY');
	$RefID = io::geti('RefID');
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

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = 'Add/Edit Goal';
	$edit->saveAndAdd = FALSE;
	$edit->topButtons = TRUE;

	$edit->setSourceTable('webset_tx.std_sb_goals', 'grefid');

	$edit->addGroup('General Information');
	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset_tx.std_sb_goals
                     WHERE iepyear = " . $stdIEPYear . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Subject', 'select')
		->sqlField('subrefid')
		->name('subrefid')
		->sql("
			SELECT subrefid,
				   subject
			  FROM webset_tx.def_sb_subjects
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY COALESCE(sequence,1), subject
        ");

	$edit->addControl('Specify Subject')
		->sqlField('othersub')
		->name('othersub')
		->showIf('subrefid', db::execSQL("
                                  SELECT subrefid
                                    FROM webset_tx.def_sb_subjects
                                   WHERE SUBSTRING(LOWER(subject), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Timeframe', 'select')
		->sqlField('timeframe_id')
		->name('timeframe_id')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_Timeframe'
			 ORDER BY refid
        ");

	$edit->addControl('Specify Timeframe')
		->sqlField('timeframe_oth')
		->name('timeframe_oth')
		->showIf('timeframe_id', db::execSQL("
                                  SELECT refid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other'
								     AND valuename = 'TX_Timeframe'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Date By', 'date')
		->sqlField('timeframe_dt')
		->name('timeframe_dt')
		->showIf('timeframe_id', db::execSQL("
                                  SELECT refid
                                    FROM webset.glb_validvalues
                                   WHERE validvalue = 'By'
								     AND valuename = 'TX_Timeframe'
                                 ")->indexAll());

	$edit->addControl('Student')
		->sqlField('stdname')
		->name('stdname')
		->value($ds->safeGet('stdfirstname') . ' will');

	$edit->addControl('Verb/Action', 'select')
		->sqlField('action_id')
		->name('action_id')
		->sql("
			SELECT arefid,
				   action
			  FROM webset_tx.def_sb_action a
			 WHERE subrefid = VALUE_01
			 ORDER BY COALESCE(a.sequence,1), action
        ")
		->tie('subrefid');

	$edit->addControl('Specify Verb/Action if Other')
		->sqlField('action_oth')
		->name('action_oth')
//		->showIf('action_id', db::execSQL("
//                                  SELECT subrefid
//                                    FROM webset_tx.def_sb_subjects
//                                   WHERE SUBSTRING(LOWER(subject), 1, 5) = 'other'
//                                 ")->indexAll())
		->size(20);

	$edit->addControl('State Behavior')
		->sqlField('behavior')
		->name('behavior')
		->size(50);

	$edit->addControl('Condition')
		->sqlField('condition')
		->name('condition')
		->size(50);

	$edit->addControl('Criteria', 'select')
		->sqlField('criteria_id')
		->name('criteria_id')
		->sql("
			SELECT ctrefid,
				   criteria
			  FROM webset_tx.def_sb_criteria
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY COALESCE(sequence,1), criteria
        ");

	$edit->addControl('Specify Criteria')
		->sqlField('criteria_oth')		
		->name('criteria_oth')		
		->showIf('criteria_id', db::execSQL("
                                  SELECT ctrefid
                                    FROM webset_tx.def_sb_criteria
                                   WHERE SUBSTRING(LOWER(criteria), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Additional Information', 'textarea')
		->sqlField('ainfo')
		->name('ainfo')
		->css('width', '100%')
		->css('height', '50px');

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
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl('ESY', 'hidden')->value($esy)->sqlField('esy');

	$url_obj = CoreUtils::getURL('std_objective_add.php', array_merge($_GET, array('RefID' => null, 'goal_id' => null)));
	$url_main = CoreUtils::getURL('std_main.php', array_merge($_GET, array('RefID' => null, 'goal_id' => ($RefID > 0 ? $RefID : null))));

	$edit->finishURL = $url_main;
	$edit->cancelURL = $url_main;

	$edit->printEdit();
?>
<script type="text/javascript">
		var edit1 = EditClass.get();
		edit1.onSaveDoneFunc(
			function(refid) {
				if ($('input[name="RefID"]').val() == 0) {
					$('input[name="RefID"]').val(refid);
					api.confirm("Do you want to add an Objective for this Goal?", onOk, onNo);
					return false;
				}
			}
		)

		function onOk() {
			api.goto(api.url('<?= $url_obj; ?>', {'RefID': 0,
				'goal_id': $('input[name="RefID"]').val()}));
		}

		function onNo() {
			api.goto(api.url('<?= $url_main; ?>', {'goal_id': $('input[name="RefID"]').val()}));
		}
</script>