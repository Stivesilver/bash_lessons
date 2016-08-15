<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Transition/On-Going Adult Services <br> <font size=2> When the student is at least 14 years of age, the IEP shall include a statement of the student\'s transition needs. A student shall be invited to any conference where transition services are discussed.';

	$edit->saveLocal = FALSE;
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '50%';

	$edit->SQL = "
		SELECT t1.stdtransitioneligibilitysw,
			   t2.parentguide,
			   t2.question1sw,
			   t2.tsscrefid,
			   course_other,
			   t2.tsdrrefid,
			   t2.question2sw,
			   t2.question3sw,
			   t2.question4sw,
			   t2.trass,
			   t2.summary,
			   t2.liveindepend,
			   t2.lastuser,
			   t2.lastupdate,
			   t1.tsrefid
		  FROM webset.sys_teacherstudentassignment AS t1
			   LEFT OUTER JOIN webset.std_in_ts AS t2 ON t2.stdrefid = t1.tsrefid
		 WHERE t1.tsrefid = " . $tsRefID . "
	";

	$edit->addGroup('General Information');

	$edit->addControl('Age', 'select')
		->data(
			array(
				''=>'Student is at least 14 years of age or turning 14',
				'Y'=>'N/A Student is not yet 14 years of age'
			)
		)
		->name('stdage');

	$edit->addControl(
		FFSwitchYN::factory('Parents have received guide for transition')
			->emptyOption(TRUE)
	)
		->name('parentguide')
		->hideIf('stdage', 'Y');

	$edit->addControl(
		FFSwitchYN::factory('Transition plan will be developed/discussed at the Annual Case Review later this year')
			->emptyOption(TRUE)
	)
		->name('question1sw')
		->hideIf('stdage', 'Y');

	$edit->addControl('Course of Study', 'select')
		->sql("
			SELECT tsscrefid,
				   tsscdesc
			  FROM webset.statedef_ts_studycourse
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (enddate IS NULL or now()< enddate)
		     ORDER BY tsscrefid
		")
		->emptyOption(true)
		->name('tsscrefid')
		->hideIf('stdage', 'Y');

	$edit->addControl('Course of Study Summary', 'textarea')
		->width('90%')
		->name('course_other')
		->sqlField('course_other')
		->help('Summarize the committee\'s discussion/ decision concerning the selection/change of the student\'s course of study.');

	$edit->addControl('Services to be discontinued due to', 'select')
		->sql("
			SELECT tsdrrefid,
			       tsdrdesc
			  FROM webset.statedef_ts_disconreason
			 WHERE screfid =  " . VNDState::factory()->id . "
		     ORDER BY 1
		")
		->emptyOption(true)
		->name('tsdrrefid')
		->hideIf('stdage', 'Y');

	$edit->addControl(
		FFSwitchYN::factory('At a case conference prior to this student\'s 17th birthday, the student and parents have been informed that at age 18 educational rights pass to him/her unless a guardian has been appointed by the Court')
			->emptyOption(TRUE)
	)
		->name('question2sw')
		->hideIf('stdage', 'Y');

	$edit->addControl('A copy of the transition folder must be given to parents at each Annual Case Review at which transition is discussed', 'hidden')
		->name('question3sw');

	$edit->addControl(
		FFSwitchYN::factory('It is anticipated that the student will need ongoing adult services after graduation or exiting the secondary education program')
			->emptyOption(TRUE)
	)
		->name('question4sw')
		->hideIf('stdage', 'Y');

	$edit->addControl('Transition Assessment')
		->width('90%')
		->name('trass')
		->hideIf('stdage', 'Y');

	$edit->addControl('Assessment Summary of Results', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->help('Summary to include information about Education/Training, Employment and Independent living Skills and progress on implementation/completion of transition services')
		->name('summary');

	$edit->addControl(
		FFSwitchYN::factory('Based on transition assessment and discussion of student\'s independent living skills with student and/or family, the student plans to live independently post-school.')
			->emptyOption(TRUE)
	)
		->name('liveindepend');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->finishURL = CoreUtils::getURL('srv_ts_save.php', array('dskey' => $dskey));
    $edit->saveURL = CoreUtils::getURL('srv_ts_save.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_ts')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();



?>
