<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$esy = io::get('ESY');
	$baseline_id = io::geti('baseline_id');
	$student = IDEAStudent::factory($tsRefID);

	if ($RefID > 0) {
		$goal = array_shift(db::execSQL("
                            SELECT *
                              FROM webset.std_bgb_goal
                             WHERE grefid = " . $RefID . "
            ")->assocAll());
	}
	
	$prevGoal = db::execSQL("
		SELECT *
          FROM webset.std_bgb_goal
		 WHERE stdrefid = " . $tsRefID . "
	     ORDER BY grefid DESC
		 LIMIT 1
	")->assoc();

	$edit = new EditClass("edit1", io::get("RefID"));

	$edit->title = 'Add/Edit ' . ($esy == 'Y' ? 'ESY ' : '') . 'Goal';
	$edit->topButtons = TRUE;

	$edit->setSourceTable('webset.std_bgb_goal', 'gRefID');

	$edit->addControl("", "select_radio")
		->name('compose')
		->value((isset($goal['overridetext']) && $goal['overridetext'] != '') ? 2 : 1)
		->data(array(1 => 'Compose Goal', 2 => 'Own Goal'));

	$edit->addGroup('Services Information');

	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset.std_bgb_goal
                     WHERE blrefid = " . $baseline_id . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Service Type', 'select')
		->sqlField('txs_servtype')
		->name('txs_servtype')
		->value($prevGoal['txs_servtype'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceType'
			 ORDER BY refid
		");

	$edit->addControl('Service Start Date', 'date')
		->sqlField('txs_durbeg')
		->name('txs_durbeg')
		->value($student->getDate('stdenrolldt'));

	$edit->addControl('Service End Date', 'date')
		->sqlField('txs_durend')
		->name('txs_durend')
		->value($student->getDate('stdcmpltdt'));

	$edit->addControl('Location', 'select')
		->sqlField('txs_location')
		->name('txs_location')		
		->value($prevGoal['txs_location'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceLoc'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Specify Location')
		->sqlField('txs_locationoth')
		->name('txs_locationoth')
		->value($prevGoal['txs_locationoth'])
		->showIf('txs_location', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceLoc'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Implementors', 'select')
		->sqlField('txs_implement')
		->name('txs_implement')
		->value($prevGoal['txs_implement'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceLoc'
			 ORDER BY sequence_number
        ");

	$edit->addControl('Specify Implementors')
		->sqlField('txs_implementoth')
		->name('txs_implementoth')
		->value($prevGoal['txs_implementoth'])
		->showIf('txs_implement', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceLoc'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup("General Information");
	$edit->addControl("Sentence Preface", "select")
		->sqlField('gpreface')
		->sql("
            SELECT gsfrefid,
                   replace(gsptext,'The student', '" . $ds->safeGet('stdfirstname') . "')
              FROM webset.disdef_bgb_goalsentencepreface
             WHERE vndRefID = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gsptext
        ")
		->hideIf('compose', 2);

	$edit->addControl("Sentence Verb", "select")
		->sqlField('gaction')
		->sql("
            SELECT gdskgarefid,
                   gdskgaaction
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_ksaksgoalactions dis ON std.blksa = dis.gdskgrefid
             WHERE blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gdskgaaction
        ")
		->hideIf('compose', 2);

	$edit->addControl("Sentence Content", "select")
		->sqlField('gcontent')
		->sql("
            SELECT gdskgcrefid,
                   gdskgccontent
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_scpksaksgoalcontent dis ON std.blksa = dis.gdskgrefid
             WHERE std.blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gdskgccontent
        ")
		->hideIf('compose', 2);

	$edit->addControl("Condition", "select")
		->sqlField('gconditions')
		->sql("
            SELECT crefid,
                   cdesc
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_ksaconditions dis ON std.blksa = dis.blksa
             WHERE std.blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY cdesc
        ")
		->hideIf('compose', 2);

	$edit->addControl("Criteria Unit", "select")
		->sqlField('dcurefid')
		->sql("
            SELECT dcurefid,
                   dcudesc
              FROM webset.disdef_bgb_criteriaunits
             WHERE vndRefId = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY dcudesc
        ")
		->hideIf('compose', 2);

	$edit->addControl("Criteria Description", "select")
		->sqlField('gcriteria')
		->sql("
            SELECT crrefid,
                   crdesc
             FROM webset.std_bgb_baseline std
                  INNER JOIN webset.disdef_bgb_ksacriteria dis ON std.blksa = dis.blksa
            WHERE std.blrefid = " . $baseline_id . "
              AND (enddate IS NULL or now()< enddate)
            ORDER BY crdesc
        ")
		->hideIf('compose', 2);

	$edit->addControl("Criteria Basis", "edit")
		->sqlField('gcriteria2')
		->size(55)
		->maxlength(1000)
		->hideIf('compose', 2);

	$edit->addControl(VNDState::factory()->code == 'KS' ? 'Timeframe' : 'Evaluation', "select")
		->sqlField('gevaluation')
		->sql("
            SELECT erefid,
                   edesc
              FROM webset.disdef_bgb_ksaeval
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY edesc
        ")
		->hideIf('compose', 2);

	$edit->addControl("Measurement", "select")
		->sqlField('gmeasure')
		->sql("
            SELECT mrefid, mdesc
              FROM webset.disdef_bgb_measure
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY mdesc
        ")
		->hideIf('compose', 2);

	$edit->addControl('Goal', 'textarea')
		->sqlField('overridetext')
		->css('width', '100%')
		->css('height', '200px')
		->hideIf('compose', 1);

	$edit->addGroup('Progress Reporting');
	$edit->addControl('Schedule For Evaluation', 'select')
		->sqlField('txs_schedule')
		->name('txs_schedule')
		->value($prevGoal['txs_schedule'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceSchedule'
			 ORDER BY refid
        ");

	$edit->addControl('Specify Schedule')
		->sqlField('txs_scheduleoth')
		->name('txs_scheduleoth')
		->value($prevGoal['txs_scheduleoth'])
		->showIf('txs_schedule', db::execSQL("
                                  SELECT validvalueid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other' 
								     AND valuename = 'TXServiceSchedule'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Notify of progress by', 'select_check')
		->sqlField('txs_notice')
		->name('txs_notice')
		->value($prevGoal['txs_notice'])
		->sql("
			SELECT validvalueid, validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TXServiceNotice'
			 ORDER BY refid
        ");

	$edit->addControl('Other Method')
		->sqlField('txs_noticeoth')
		->name('txs_noticeoth')
		->value($prevGoal['txs_noticeoth'])
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('ESY', 'hidden')->value($esy)->sqlField('esy');
	$edit->addControl('Baseline ID', 'hidden')->value($baseline_id)->sqlField('blrefid');

	$url_bench = CoreUtils::getURL('bgb_benchmark_add.php', array_merge($_GET, array('RefID' => null, 'goal_id' => null)));
	$url_main = CoreUtils::getURL('bgb_main.php', array_merge($_GET, array('RefID' => null, 'goal_id' => ($RefID > 0 ? $RefID : null))));

	$edit->finishURL = $url_main;
	$edit->cancelURL = $url_main;

	$edit->setPresaveCallback('goalCompose', 'bgb_save.inc.php');

	$edit->saveAndAdd = false;

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
			api.goto(api.url('<?= $url_bench; ?>', {'RefID': 0,
				'goal_id': $('input[name="RefID"]').val()}));
		}

		function onNo() {
			api.goto(api.url('<?= $url_main; ?>', {'goal_id': $('input[name="RefID"]').val()}));
		}
</script>