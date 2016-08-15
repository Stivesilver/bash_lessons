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
	$edit->topButtons = true;

	$edit->setSourceTable('webset.std_bgb_goal', 'gRefID');

	$edit->addControl("", "select_radio")
		->name('compose')
		->value((isset($goal['overridetext']) && $goal['overridetext'] != '') ? 2 : 1)
		->data(array(1 => 'Compose Goal', 2 => 'Own Goal'));

	$edit->addGroup("General Information");

	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int)db::execSQL("
                    SELECT max(order_num)
                      FROM webset.std_bgb_goal
                     WHERE blrefid = " . $baseline_id . "
                ")->getOne() + 1
		)
		->size(5);

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
	$edit->addControl(FFMultiSelect::factory('Eval. Procedure', 'select'))
		->sqlField('used_stand')
		->name('dcurefid')
		->sql("
			SELECT refid,
			       validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'CT_Eval_Proc' AND ( ((CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') )
			 ORDER BY valuename, sequence_number, validvalue ASC
        ");


	$edit->addControl('Specify')
		->sqlField('txs_scheduleoth')
		->name('txs_scheduleoth')
		->showIf('dcurefid', db::execSQL("
				SELECT refid
				  FROM webset.glb_validvalues
				 WHERE LOWER(validvalue) LIKE '%other%'
            ")->indexAll())
		->size(50);

	$edit->addControl('Perf. Criteria:', 'select')
		->sqlField('gcriteria')
		->name('gcriteria')
		->sql("
			SELECT refid,
			       validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'CT_Perform_Criteria' AND ( ((CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') )
			 ORDER BY valuename, sequence_number, validvalue ASC
		")
		->emptyOption(true);


	$edit->addControl('Specify')
		->sqlField('txs_implementoth')
		->name('txs_implementoth')
		->showIf('gcriteria', db::execSQL("
				SELECT refid
				  FROM webset.glb_validvalues
				 WHERE LOWER(validvalue) LIKE '%other%'
            ")->indexAll())
		->size(50);

	$edit->addControl('(%, Trials, etc.)')
		->sqlField('txs_noticeoth')
		->name('txs_noticeoth')
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

	print UILayout::factory()
		->addHTML(UIAnchor::factory(UIMessage::factory('CONNECTICUT CORE STANDARDS', UIMessage::NOTE)->toHTML())
			->onClick('ccoreView();')
			->toHTML(), 'bold')
		->toHTML();
?>
<script type="text/javascript">
	var edit1 = EditClass.get();
	var recordAdded = false;

	function ccoreView() {
		var win = api.window.open("Core Standards List",
			api.url("./ccore_list.php")
		);
		win.resize(700, 800);
		win.show();
	}

	edit1.onSaveFunc(
		function () {
			if (edit1.refid == 0) {
				recordAdded = true;
			}
		}
	)

	edit1.onSaveDoneFunc(
		function (refid) {
			if (recordAdded) {
				api.confirm("Do you want to add a Benchmark for this Goal?", onOk, onNo);
				return false;
			}
		}
	)

	function onOk() {
		api.goto(api.url('<?= $url_bench; ?>', {'RefID': 0,
			'goal_id': edit1.refid}));
	}

	function onNo() {
		api.goto(api.url('<?= $url_main; ?>', {'goal_id': edit1.refid}));
	}

	function ccoreView() {
		var win = api.window.open('Core Standards List',
			api.url('./ccore_list.php')
		);
		win.resize(700, 800);
		win.addEventListener('ccore_selected', onEvent);
		win.show();
	}

	function onEvent(e) {
		var selected = e.param.selected;
		var colValue = $('#used_stand').val();
		if (colValue != '') {
			$('#used_stand').val(colValue + ', ' + selected);
		} else {
			$('#used_stand').val(selected);
		}
	}
</script>

<script type="text/javascript">
	var edit1 = EditClass.get();
	var recordAdded = false;

	edit1.onSaveFunc(
		function () {
			if (edit1.refid == 0) {
				recordAdded = true;
			}
		}
	)

	edit1.onSaveDoneFunc(
		function (refid) {
			if (recordAdded) {
				api.confirm("Do you want to add a Objective for this Goal?", onOk, onNo);
				return false;
			}
		}
	)

	function onOk() {
		api.goto(api.url('<?= $url_bench; ?>', {'RefID': 0,
			'goal_id': edit1.refid}));
	}

	function onNo() {
		api.goto(api.url('<?= $url_main; ?>', {'goal_id': edit1.refid}));
	}
</script>

