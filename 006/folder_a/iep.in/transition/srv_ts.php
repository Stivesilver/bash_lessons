<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$set_id = IDEAFormat::get('id');
	$ini = IDEAFormat::getIniOptions();

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'Transition/On-Going Adult Services <br> <font size=2> When the student is at least 14 years of age, the IEP shall include a statement of the student\'s transition needs. A student shall be invited to any conference where transition services are discussed.';

	$edit->saveLocal = FALSE;
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '50%';

	$edit->SQL = "
		SELECT t1.stdtransitioneligibilitysw,
			   t2.tsscrefid,
			   t2.course_other,
			   t2.tsdrrefid,
			   t2.question2sw,
			   NULL,
			   t2.question4sw,
			   t2.trass,
			   t2.summary,
			   t2.liveindepend,
			   t2.disclose_sw,
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
				'' => 'Student is at least 14 years of age or turning 14',
				'Y' => 'N/A Student is not yet 14 years of age'
			)
		)
		->name('stdage');

	$edit->addControl('Course of Study', 'select')
		->sql("
			SELECT tsscrefid,
				   tsscdesc
			  FROM webset.statedef_ts_studycourse
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND COALESCE(set_id, " . $set_id . ") = " . $set_id . "
			   AND (enddate IS NULL or now()< enddate)
		     ORDER BY tsscrefid
		")
		->emptyOption(true)
		->sqlField('tsscrefid')
		->name('tsscrefid')
	;

	$edit->addControl('Specify')
		->width('90%')
		->name('course_other')
		->sqlField('course_other')
		->showIf('tsscrefid', db::execSQL("
				SELECT tsscrefid
				  FROM webset.statedef_ts_studycourse
				 WHERE LOWER(tsscdesc) LIKE '%other%'
            ")->indexAll());

	$edit->addControl('Services to be discontinued due to', 'select')
		->sql("
			SELECT tsdrrefid,
			       tsdrdesc
			  FROM webset.statedef_ts_disconreason
			 WHERE screfid =  " . VNDState::factory()->id . "
		     ORDER BY 1
		")
		->sqlField('tsdrrefid')
		->emptyOption(true)
		->name('tsdrrefid');

	$edit->addControl(
			FFSwitchYN::factory('At a case conference prior to this student\'s 17th birthday, the student and parents have been informed that at age 18 educational rights pass to him/her unless a guardian has been appointed by the Court')
			->emptyOption(TRUE)
		)
		->sqlField('question2sw')
		->name('question2sw');

	$edit->addControl('Notice of Transfer of Parental Rights', 'protected')
		->append(
			file_exists(CoreUtils::getPhysicalPath($ini['in_guardianship_form_file'])) ?
				UIAnchor::factory('Open Form')->onClick('openForm()')->toHTML() :
				UIMessage::factory('Form has not yet set up.')
		)
		->showIf('question2sw', 'Y');

	$edit->addControl(
			FFSwitchYN::factory('It is anticipated that the student will need ongoing adult services after graduation or exiting the secondary education program')
			->emptyOption(TRUE)
		)
		->sqlField('question4sw')
		->name('question4sw');

	$edit->addControl('Transition Assessment')
		->width('90%')
		->sqlField('trass')
		->name('trass');

	$edit->addControl('List student\'s preferences and interests as indicated on the Transition Assessments listed:', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->help('Summary to include information about Education/Training, Employment and Independent living Skills and progress on implementation/completion of transition services')
		->sqlField('summary')
		->name('summary');

	$edit->addControl(
			FFSwitchYN::factory('Based on transition assessment and discussion of student\'s independent living skills with student and/or family, the student plans to live independently post-school.')
			->emptyOption(TRUE)
		)
		->sqlField('liveindepend')
		->name('liveindepend');

	$edit->addControl(
			FFSwitchYN::factory('Parent consent to disclose educational records to Vocational Rehabilitation and to invite Vocational Rehabilitation to the next ACR')
			->emptyOption(TRUE)
		)
		->sqlField('disclose_sw')
		->name('disclose_sw');

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
<script type="text/javascript">

		function openForm() {
			url = api.url('srv_ts_guardianship.ajax.php');
			api.ajax.process(ProcessType.REPORT, url);
		}

</script>
