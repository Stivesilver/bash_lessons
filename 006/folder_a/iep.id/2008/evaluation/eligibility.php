<?php

	Security::init();

	$dskey    = io::get('dskey');
	$ds       = DataStorage::factory($dskey);
	$tsRefID  = $ds->safeGet('tsRefID');
	$area_id  = 36;

	$RefID = db::execSQL("
		SELECT refid
          FROM webset.std_general
         WHERE stdrefid = $tsRefID
           AND area_id = $area_id
        ")->getOne();

	$edit = new EditClass('edit1', (int) $RefID);


	$edit->finishURL = 'javascript:parent.switchTab(-1);';
	$edit->saveAndEdit    = true;
	$edit->saveAndAdd = false;
	$edit->firstCellWidth = "30%";

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->title = "Eligibility Determination";

	$edit->addGroup("General Information (See Note 1)");
	$edit->addControl("In consideration of the reported information, the evaluation team finds the student", "select")
		->sqlField('txt01')
		->name('txt01')
		->sql("
			SELECT validvalueid,
                   validvalue
	          FROM webset.glb_validvalues
	         WHERE valuename = 'ID_Sp_Ed_eligibility'
	           AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
	         ORDER BY valuename, sequence_number, validvalue
		")
		->emptyOption(true);

	$edit->addControl(FFMultiSelect::factory("Under the category"))
		->sql("
			SELECT elrefid,
			       eldesc
			  FROM webset.es_statedef_eligibility AS t
			 WHERE screfid = ".VNDState::factory()->id."
			   AND COALESCE(recdeactivationdt, now()) >= now()
			 ORDER BY seqnum, elcode, eldesc
		")
		->maxRecords(1)
		->sqlField('int02');

	$edit->addControl("Date determination was made", "date")
		->sqlField('dat01')
		->name('dat01')
		->req(true)
		->onChange('calculateDate()');

	$edit->addGroup("Early Childhood (See Note 2)");

	$edit->addControl("Age of student as of the determination date", 'text')
		->transparent(true)
		->width('90%')
		->readOnly(true)
		->name('txt03')
		->sqlField('txt03');

	$reason = db::execSQL("
		SELECT validvalueid,
               validvalue || COALESCE(' (' || validvalueid || ')', '') AS name
          FROM webset.glb_validvalues
         WHERE valuename = 'ID_Eval_Late_Reason'
           AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
         ORDER BY valuename, sequence_number, validvalue
		")->assocAll();

	$count = count($reason);

	$options[''] = 'Not Selected';

	for ($i = 0; $i < $count; $i++) {
		$key = $reason[$i]['validvalueid'];

		$options[$key] = $reason[$i]['name'];
	}

	$edit->addControl("Reason for delay in determination", "select")
		->sqlField('txt04')
		->name('txt04')
		->data($options);

	$options = null;

	$edit->addControl("Explanation of Other", "edit")
		->sqlField('txt05')
		->size(80)
		->maxlength(250)
		->showIf('txt04', 'O');

	$edit->addGroup("Determination Timeline (See Note 3)");
	$edit->addControl("Number of days from the current eval to the determination date", "text")
		->transparent(true)
		->readOnly(true)
		->name('int01')
		->sqlField('int01');

	$reason = db::execSQL("
		SELECT validvalueid,
               validvalue || COALESCE(' (' || validvalueid || ')', '') as name
          FROM webset.glb_validvalues
         WHERE valuename = 'ID_SpEd_Late_Reason'
           AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
         ORDER BY valuename, sequence_number, validvalue
		")->assocAll();

	$count = count($reason);

	$options[''] = 'Not Selected';

	for ($i = 0; $i < $count; $i++) {
		$key = $reason[$i]['validvalueid'];

		$options[$key] = $reason[$i]['name'];
	}

	$edit->addControl("Reason for late determination", "select")
		->name('txt06')
		->sqlField('txt06')
		->data($options);

	$options = null;

	$explanation = db::execSQL("
		SELECT validvalueid,
        	   validvalue || COALESCE(' (' || validvalueid || ')', '') as name
       	  FROM webset.glb_validvalues
       	 WHERE valuename = 'ID_Exception_Rule'
           AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
       	 ORDER BY valuename, sequence_number, validvalue
		")->assocAll();

	$count = count($explanation);

	$options[''] = 'Not Selected';

	for ($i = 0; $i < $count; $i++) {
		$key = $explanation[$i]['validvalueid'];

		$options[$key] = $explanation[$i]['name'];
	}

	$edit->addControl("Explanation of SE (State Exception Rule)", "select")
		->sqlField('txt07')
		->data($options)
		->showIf('txt06', 'SE');

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($area_id)
		->sqlField('area_id');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

    $edit->printEdit();

	#notes
	echo UILayout::factory()
		->addHTML('Note 1:', '[padding-left: 20px;]')
		->newLine()
		->addHTML('A. If considering a Learning Disability category, after completing this report you must also complete the LD Eligibility form (Form # 400a or # 400b).', '[padding-left: 20px;]')
		->newLine()
		->addHTML('B. All relevant documentation, reports, and observations must be attached to this eligibility report.', '[padding-left: 20px;]')
		->newLine()
		->addHTML('C. A copy of this report and all attachments must be given to the parent or adult student.', '[padding-left: 20px;]')
		->newLine()
		->addHTML('Note 2:', '[padding-left: 20px;]')
		->newLine()
		->addHTML('A. If the age of the student as of the determination date is greater than three, a reason for the delay in determination will need to be selected.', '[padding-left: 20px;]')
		->newLine()
		->addHTML('B. If the reason for the delay in determination is Other, an explanation will need to be entered.', '[padding-left: 20px;]')
		->newLine()
		->addHTML('Note 3:', '[padding-left: 20px;]')
		->newLine()
		->addHTML('A. If the determination of eligibility was more than 60 days after the current eval, a reason for the late determination will need to be selected.', '[padding-left: 20px;]')
		->newLine()
		->addHTML('B. If the reason for the late determination is SE (State Exception Rule), an explanation will need to be selected.', '[padding-left: 20px;]')
		->toHTML();

	io::jsVar('tsRefID', $tsRefID);


?>

<script type="text/javascript">

	function calculateDate() {
		var date = $('#dat01').val();

		api.ajax.post(
			'./eligibility_check.ajax.php',
			{date: date, tsRefID: tsRefID},
			function(result) {
				$('#txt03').val(result.date);
				$('#int01').val(result.age);
			}
		);
	}

	calculateDate();

</script>
