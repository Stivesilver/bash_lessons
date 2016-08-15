<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$RefID = io::geti('RefID');
	$esy = io::get('ESY');
	$ideaStudent = IDEAStudent::factory($tsRefID);
	$edit = new EditClass('edit1', $RefID);
	$orderNum = db::execSQL("
    	SELECT max(order_num)
              FROM webset.std_oth_goals
             WHERE iepyear=$stdIEPYear
               AND esy='" . $esy . "'
        ")->getOne() + 1;

	$edit->setSourceTable('webset.std_oth_goals', 'grefid');

	$edit->topButtons = true;
	$edit->title = "Add/Edit Goal";
	$edit->firstCellWidth = '30%';

	$edit->addGroup("General Information");
	$edit->addControl("Order #", "integer")
		->value($orderNum)
		->sqlField('order_num')
		->size(4);

	$edit->addControl(
		FFMultiSelect::factory('Area')
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
			->maxRecords(1)
			->name('areaID')

	)
		->sqlField('area_id')
	->req();

	$edit->addControl("1. Present Level of Performance", "edit")
		->sqlField('plafp')
		->size(60);

	$edit->addControl("2. General Education Content Standard(s):<br> <a href=\"javascript:loadpage('http://www.sde.idaho.gov/site/content_standards');\">Idaho Content Standards with Limits</a>", "textarea")
		->sqlField('gen_edu')
		->autoHeight(true);

	$edit->addGroup("3. Annual Goal");
	$edit->addControl("Goal Type", "select_radio")
		->value("C")
		->sqlField('goal_type')
		->name('goal_type')
		->sql("
			SELECT validvalueid,
                   validvalue
       		  FROM webset.glb_validvalues
		     WHERE ValueName = 'ID_Goal_Type'
		");

	$edit->addControl("Condition", "select")
		->sqlField('cond_id')
		->name('cond_id')
		->sql("
			SELECT crefid,
			       cdesc
			  FROM webset.disdef_bgb_ksaconditions
			 WHERE blksa = VALUE_01
			   AND umrefid = USERID
			   AND (enddate IS NULL or now()< enddate)
			 ORDER BY 2
		 ")
		->tie('areaID')
		->hideIf('goal_type', "O")
		->emptyOption(true, 'Other', 0);

	$edit->addControl("Specify Condition", "edit")
		->sqlField('cond_oth')
		->name('cond_oth')
		->append(UICustomHTML::factory(UIAnchor::factory())
				->id('new-cond_oth')
				->css('padding-left', '20px')
				->onClick('addNewBank("condition", 1, "cond_oth")')
		)
		->size(60)
		->hideIf('cond_id', db::execSQL("
                                  SELECT crefid
			  						FROM webset.disdef_bgb_ksaconditions
			 					    WHERE umrefid = USERID
                                 ")->indexAll())
		->hideIf('goal_type', "O")
		->onChange('addNewBank("condition", 0, "cond_oth")');

	$edit->addControl("Student", "edit")
		->value("at the " . $ideaStudent->get('grdlevel') .
			" grade level, " . str_replace("''", "'", $ideaStudent->get('stdfirstname')) . " will")
		->sqlField('stdname')
		->size(60)
		->hideIf('goal_type', "O");

	$edit->addControl("Sentence Verb", "select")
		->sqlField('verb_id')
		->name('verb_id')
		->sql("
		 	 SELECT gdskgarefid,
                    gdskgaaction
			   FROM webset.disdef_bgb_ksaksgoalactions
			  WHERE gdskgrefid = VALUE_01
                AND umrefid = USERID
                AND (enddate IS NULL or now()< enddate)
              ORDER BY 2
		 	 ")
		->tie('areaID')
		->hideIf('goal_type', "O")
		->emptyOption(true, 'Other', 0);

	$edit->addControl("Specify Verb:", "edit")
		->sqlField('verb_oth')
		->name('verb_oth')
		->size(60)
		->hideIf('goal_type', "O")
		->hideIf('verb_id', db::execSQL("
                                  SELECT gdskgarefid
			  						FROM webset.disdef_bgb_ksaksgoalactions
			 					   WHERE umrefid = USERID
                                 ")->indexAll())
		->append(UICustomHTML::factory(UIAnchor::factory())
				->id('new-verb_oth')
				->css('padding-left', '20px')
				->onClick('addNewBank("verb", 1, "verb_oth")')
		)
		->onChange('addNewBank("verb", 0, "verb_oth")');

	$edit->addControl("Sentence Content:", "select")
		->sqlField('content_id')
		->name('content_id')
		->sql("
		 	 SELECT gdskgcrefid,
                    gdskgccontent
			   FROM webset.disdef_bgb_scpksaksgoalcontent
			  WHERE gdskgrefid = VALUE_01
                AND umrefid = USERID
                AND (enddate IS NULL or now()< enddate)
              ORDER BY 2
             ")
		->tie('areaID')
		->hideIf('goal_type', "O")
		->emptyOption(true, 'Other', 0);

	$edit->addControl("Specify Content:", "edit")
		->sqlField('content_oth')
		->name('content_oth')
		->size(60)
		->hideIf('goal_type', "O")
		->hideIf('content_id', db::execSQL("
                                  SELECT gdskgcrefid
			  						FROM webset.disdef_bgb_scpksaksgoalcontent
			 					   WHERE umrefid = USERID
                                 ")->indexAll())
		->append(UICustomHTML::factory(UIAnchor::factory())
				->id('new-content_oth')
				->css('padding-left', '20px')
				->onClick('addNewBank("content", 1, "content_oth")')
		)
		->onChange('addNewBank("content", 0, "content_oth")');

	$edit->addControl("Measurable Element:", "select")
		->sqlField('meas_id')
		->name('meas_id')
		->sql("
		 	 SELECT mrefid,
                    mdesc
               FROM webset.disdef_bgb_measure
              WHERE umrefid = USERID
                AND (enddate IS NULL or now()< enddate)
              ORDER BY 2
             ")
		->hideIf('goal_type', "O")
		->emptyOption(true, 'Other', 0);

	$edit->addControl("Specify Measurable Element:", "edit")
		->sqlField('meas_oth')
		->name('meas_oth')
		->size(60)
		->hideIf('goal_type', "O")
		->hideIf('meas_id', db::execSQL("
                                  SELECT mrefid
			  						FROM webset.disdef_bgb_measure
			 					   WHERE umrefid = USERID
                                 ")->indexAll())
		->append(UICustomHTML::factory(UIAnchor::factory())
				->id('new-meas_oth')
				->css('padding-left', '20px')
				->onClick('addNewBank("measure", 1, "meas_oth")')
		)
		->onChange('addNewBank("measure", 0, "meas_oth")');

	$edit->addControl("Specify By When:", "edit")
		->sqlField('timeframe_oth')
		->size(30)
		->hideIf('goal_type', "O");

	$edit->addControl("3. Annual Goal (target skill and conditions)", "textarea")
		->sqlField('own_goal')
		->css("width", "100%")
		->css("height", "100px")
		->hideIf('goal_type', "C")
		->autoHeight(true);

	$edit->addGroup("4. Evaluation Procedure");
	$edit->addControl("Specify Procedure:", "edit")
		->sqlField('proc_oth')
		->size(60)
		->hideIf('goal_type', "O");

	$edit->addControl("Specify Criteria:", "edit")
		->sqlField('criteria_oth')
		->size(60)
		->hideIf('goal_type', "O");

	$edit->addControl("Grade Level (if needed):", "edit")
		->sqlField('grade_eval')
		->size(60)
		->hideIf('goal_type', "O");

	$edit->addControl("Schedule:", "select")
		->sqlField('sched_id')
		->name('sched_id')
		->sql("
            SELECT erefid,
                   edesc
              FROM webset.disdef_bgb_ksaeval
             WHERE umrefid = " . SystemCore::$userID . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY 2
            ")
		->emptyOption(true, 'Other', 0)
		->hideIf('goal_type', "O");

	$edit->addControl("Specify Schedule:", "edit")
		->sqlField('sched_oth')
		->name('sched_oth')
		->size(60)
		->hideIf('sched_id', db::execSQL("
                                  SELECT erefid
			  						FROM webset.disdef_bgb_ksaeval
			 					   WHERE umrefid = USERID
                                 ")->indexAll())
		->hideIf('goal_type', "O")
		->append(UICustomHTML::factory(UIAnchor::factory())
				->id('new-sched_oth')
				->css('padding-left', '20px')
				->onClick('addNewBank("schedule", 1, "sched_oth")')
		)
		->onChange('addNewBank("schedule", 0, "sched_oth")');

	$edit->addControl("4. Evaluation Procedure (criteria, procedure, and schedule):", "textarea")
		->sqlField('own_eval')
		->hideIf('goal_type', "C")
		->autoHeight(true);

	$edit->addGroup("AT and Progress");
	$edit->addControl("5. Assistive Technology (if needed):", "edit")
		->sqlField('ass_tech')
		->size(60);

	$edit->addControl("6. How and When Progress Toward Goals Is Reported:", "edit")
		->sqlField('progress')
		->size(60);

	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")
		->value($_SESSION["s_userUID"])
		->sqlField('lastuser');

	$edit->addControl("Last Update", "protected")
		->value(date("m-d-Y H:i:s"))
		->sqlField('lastupdate');

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addControl("esy", "hidden")
		->value(io::get("ESY"))
		->sqlField('esy');

	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->value("");

	$edit->printEdit();

?>

<script type="text/javascript">

	function addNewBank(area, add, id) {
		var bank = $('#' + id).val();
		var areaID = $('#areaID').val();
		url = api.url('oth_main_add_condition.ajax.php');
		api.ajax.post(
			url,
			{
				'area': area,
				'bank': bank,
				'area_id': areaID,
				'add': add
			},
			function (answer) {
				if (answer.data == 'added') {
					answer.data = '';
				}

				$('#new-' + id + ' .zLink').text(answer.data);
			}
		);
	}

</script>
