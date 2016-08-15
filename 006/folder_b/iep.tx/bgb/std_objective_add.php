<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$esy = io::get('ESY');
	$goal_id = io::geti('goal_id');

	$goal = array_shift(db::execSQL("
                            SELECT g.*, SUBSTRING(LOWER(validvalue), 1, 5) as timeframe
                              FROM webset_tx.std_sb_goals g
							       INNER JOIN webset.glb_validvalues v ON timeframe_id = refid
                             WHERE grefid = " . $goal_id . "
            ")->assocAll());

	if ($RefID > 0) {
		$bench = array_shift(db::execSQL("
                            SELECT *
                              FROM webset_tx.std_sb_objectives
                             WHERE orefid = " . $RefID . "
            ")->assocAll());
	}

	$edit = new EditClass("edit1", io::get("RefID"));

	$edit->title = 'Add/Edit ' . ($esy == 'Y' ? 'ESY ' : '') . 'Objective';

	$edit->setSourceTable('webset_tx.std_sb_objectives', 'orefid');

	$edit->addGroup("General Information");
	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset_tx.std_sb_objectives
                     WHERE grefid = " . $goal_id . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Timeframe', 'select')
		->sqlField('timeframe_id')
		->name('timeframe_id')		
		->value(db::execSQL("
			SELECT refid
			  FROM webset.glb_validvalues
			 WHERE SUBSTRING(LOWER(validvalue), 1, 5) = '" . $goal['timeframe'] . "'
			   AND valuename = 'TX_ObjTimeframe'
			   
		")->getOne())
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_ObjTimeframe'
			 ORDER BY refid
        ");

	$edit->addControl('Specify Timeframe')
		->sqlField('timeframe_oth')
		->name('timeframe_oth')
		->value($goal['timeframe_oth'])
		->showIf('timeframe_id', db::execSQL("
                                  SELECT refid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other'
								     AND valuename = 'TX_ObjTimeframe'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Date By', 'date')
		->sqlField('timeframe_dt')
		->name('timeframe_dt')
		->value($goal['timeframe_dt'])
		->showIf('timeframe_id', db::execSQL("
                                  SELECT refid
                                    FROM webset.glb_validvalues
                                   WHERE validvalue = 'By'
								     AND valuename = 'TX_ObjTimeframe'
                                 ")->indexAll());

	$edit->addControl('Student')
		->sqlField('stdname')
		->name('stdname')
		->value($goal['stdname']);

	$edit->addControl('Verb/Action', 'select')
		->sqlField('action_id')
		->name('action_id')		
		->value($goal['action_id'])
		->sql("
			SELECT arefid,
				   action
			  FROM webset_tx.def_sb_action a
			 WHERE subrefid = " . $goal['subrefid'] . "
			 ORDER BY COALESCE(a.sequence,1), action
        ");

	$edit->addControl('Specify Verb/Action if Other')
		->sqlField('action_oth')
		->name('action_oth')
		->value($goal['action_oth'])
		->showIf('action_id', db::execSQL("
                                  SELECT subrefid
                                    FROM webset_tx.def_sb_subjects
                                   WHERE SUBSTRING(LOWER(subject), 1, 5) = 'other'
                                 ")->indexAll())
		->size(20);

	$edit->addControl('State Behavior')
		->sqlField('behavior')
		->name('behavior')
		->value($goal['behavior'])
		->size(50);

	$edit->addControl('Condition')
		->sqlField('condition')
		->name('condition')
		->value($goal['condition'])
		->size(50);

	$edit->addControl('Criteria', 'select')
		->sqlField('criteria_id')
		->name('criteria_id')
		->value($goal['criteria_id'])
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
		->value($goal['criteria_oth'])
		->showIf('criteria_id', db::execSQL("
                                  SELECT ctrefid
                                    FROM webset_tx.def_sb_criteria
                                   WHERE SUBSTRING(LOWER(criteria), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Additional Information', 'textarea')
		->sqlField('ainfo')
		->name('ainfo')
		->value($goal['ainfo'])
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Goal ID', 'hidden')->value($goal_id)->sqlField('grefid');

	$url_bench = CoreUtils::getURL('std_objective_add.php', array_merge($_GET, array('RefID' => null, 'goal_id' => $goal_id)));

	$edit->finishURL = CoreUtils::getURL('std_main.php', array_merge($_GET, array('RefID' => null)));
	$edit->cancelURL = CoreUtils::getURL('std_main.php', array_merge($_GET, array('RefID' => null)));

	$edit->saveAndAdd = false;

	$edit->printEdit();
?>
<script type="text/javascript">
		var edit1 = EditClass.get();
		edit1.onSaveDoneFunc(
			function(refid) {
				if ($('input[name="RefID"]').val() == 0) {
					api.confirm("Do you want to add another Objective for this Goal?", onOk, onNo);
					return false;
				}
			}
		)

		function onOk() {
			api.goto(api.url('<?= $url_bench; ?>', {'RefID': 0}));
		}

		function onNo() {
			edit1.cancelEdit();
		}
</script>