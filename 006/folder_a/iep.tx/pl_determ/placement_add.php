<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$vourefid = $ds->safeGet('vourefid');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Instructional Arrangement';
	$edit->firstCellWidth = '50%';
	$edit->setSourceTable('webset_tx.std_instruct_arrange', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('School Campus', 'select')
		->sql("
			SELECT vourefid,
				   vouname,
				   1
			  FROM sys_voumst
			 WHERE vndrefid = VNDREFID
				   UNION
			SELECT -1 AS vourefid,
				   'Other' AS vouname,
				   2
			 ORDER BY 3, vouname
		")
		->sqlField('campus_id')
		->name('campus_id')
		->value($vourefid)
		->caption('School Campus');

	$edit->addControl('Specify Name of School Campus')
		->sqlField('school_camp')
		->showIf('campus_id', array('-1'));

	$edit->addControl('Instructional Arrangement', 'select')
		->sql("
			SELECT spcrefid, spccode || ' - ' || spcdesc
			  FROM webset.statedef_placementcategorycode
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (CASE recdeactivationdt<now() WHEN true THEN 2 ELSE 1 END)=1
			 ORDER BY spccode
		")
		->sqlField('placement');

	$edit->addControl('SLC Code', 'select')
		->sql("
			SELECT refid,
			       validvalue || ' - ' || validvalueid,
			       2
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_PPCD_Ind'
			   AND (glb_enddate IS NULL OR now()< glb_enddate)
			 ORDER BY 3, 2
		")
		->sqlField('ppcdind')
		->emptyOption(true, 'N/A');

	$edit->addControl('Speech Indicator Code', 'select')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_Speech_Ind'
			   AND (glb_enddate IS NULL OR now()< glb_enddate)
			 ORDER BY 2
		")
		->sqlField('speechind')
		->emptyOption(true, 'N/A');


	$edit->addControl('Location', 'select')
		->sql("
			SELECT crtrefid, crtdesc
			  FROM webset.disdef_location
			 WHERE (enddate>now() or enddate is Null)
			   AND vndrefid = " . $_SESSION["s_VndRefID"] . "
			 ORDER BY 2
		")
		->sqlField('location')
		->emptyOption(true);

	$edit->addControl(
		FFSwitchYN::factory('This is the campus the student would attend if not disabled')
			->sqlField('camp_attend')
			->name('camp_attend')
	);

	$edit->addControl('If No, identify (list or describe) the services which cannot reasonably be provided on the student\'s home campus', 'textarea')
		->sqlField('camp_attend_no')
		->css('width', '100%')
		->css('height', '70px')
		->showIf('camp_attend', 'N');

	$edit->addControl(
		FFSwitchYN::factory('This is the campus which is as close as possible to the student\'s home')
			->sqlField('camp_close')
			->name('camp_close')
	);

	$edit->addControl('If No, justify', 'textarea')
		->sqlField('camp_close_no')
		->css('width', '100%')
		->css('height', '70px')
		->showIf('camp_close', 'N');

	$edit->addControl(
		FFSwitchYN::factory('The student has available an instructional day commensurate with that of students without disabilities')
			->sqlField('instruct_day')
			->name('instruct_day')
	);

	$edit->addControl('If No, explain', 'textarea')
		->sqlField('instruct_day_no')
		->css('width', '100%')
		->css('height', '70px')
		->showIf('instruct_day', 'N');

	$edit->addControl('Date', 'date')
		->sqlField('period_dt');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('std_refid');


	$edit->finishURL = CoreUtils::getURL('placement.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('placement.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
