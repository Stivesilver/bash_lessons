<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudent($tsRefID);

	#Finds PLAFP table ID
	$SQL = "
        SELECT prefid
          FROM webset.std_plepmst
         WHERE iepyear = " . $stdIEPYear . "
           AND stdrefid = " . $tsRefID . "
    ";

	$result = db::execSQL($SQL);
	if (!$result->EOF) {
		$prefid = $result->fields[0];
	} else {
		$prefid = 0;
	}

	#Initialises default summary text
	if ($student->get('stdage') >= 16) {
		$assessment_title = " <i>(If student is 16 years of age or older, Transition must be included. Future plans should be indicated within Transition.)</i>";
		$assessment_text = chr(13) . chr(13) . chr(13) . "Transition Assessment:" . chr(13) . chr(13) . chr(13);
	} else {
		$assessment_title = "";
		$assessment_text = "";
	}
	$ownText = IDEACore::disParam(109);
	if ($ownText != "") {
		$summaryText = $ownText;
	} else {
		$summaryText = "Physical Condition / Motor:" . chr(13) . chr(13) . chr(13) .
			"Speech and Language:" . chr(13) . chr(13) . chr(13) .
			"Intellectual / Cognitive Development / Adaptive Behavior:" . chr(13) . chr(13) . chr(13) .
			"Achievement:" . chr(13) . chr(13) . chr(13) .
			"Social / Emotional / Behavioral Functioning:" . $assessment_text;
	}

	#Starts EditClass Page
	$edit = new EditClass('edit1', $prefid);

	$edit->title = 'Present Levels of Academic Achievement and Functional Performance';

	$edit->setSourceTable('webset.std_plepmst', 'prefid');

	$edit->addControl('', 'protected')
		->prepend("<i>Functional Performance refers to general ability and problem solving, attention and organization, communication, social skills, behavior,  independent living, self-advocacy, learning style, vocational, employment</i>");

	$edit->addGroup('General Information');

	$edit->addControl('', 'textarea')
		->sqlField('pleadstat')
		->value($student->get('stdnamefml') . ", age " . $student->get('stdage') . ", grade " . $student->get('grdlevel'))
		->css('width', '100%')
		->css('height', '100px')
		->name('pleadstat')
		->prepend("
            <b>Provide Brief Lead-in Statement, which includes:</b> Student's Name, Age, and Grade
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pmtsgened')
		->css('width', '100%')
		->css('height', '100px')
		->name('pmtsgened')
		->prepend("
            <b>1. How the child's disability affects his/her involvement and progress <br>
            in the general education curriculum; or for preschool children, participation in age-appropriate
            activities.</b><br/>
            For students with transition plans, consider how the child's disability will affect the
            child's ability to reach his/her  post-secondary goals (what the child will do after high school).
        ");

	if (IDEACore::disParam(7) == 'Y') {
		$disability = array_shift($student->getDisability());
		$edit->addControl('', 'protected')
			->prepend("
                On <b>" . $student->get('stdevaldt') . "</b> the IEP Team determined that " . $student->get('stdname') . "
                met eligibility criteria for  <b> " . $disability['disability'] . "</b>
            ");
	}

	$edit->addControl('', 'textarea')
		->sqlField('pbaseline')
		->css('width', '100%')
		->css('height', '100px')
		->name('pbaseline')
		->prepend("
            <b>2. The strengths of the child</b> <br/>
            For students with transition plans, consider how the strengths of the child relate to the
            child's post-secondary goals.
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pgdconcrn')
		->css('width', '100%')
		->css('height', '100px')
		->name('pgdconcrn')
		->prepend("
            <b>3. Concerns of the parent/guardian for enhancing the education of the child</b> <br>
            For students with transition plans, consider the parent/guardian's expectations for the child after the
            child leaves high school.
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pstrstd')
		->css('width', '100%')
		->css('height', '100px')
		->name('pstrstd')
		->prepend("
            <b>4. Changes in current functioning of the child since the initial
            or prior IEP" . $assessment_title . "</b><br/>
            For students with transition plans, consider how changes in the child's current functioning will impact
            the child's ability to reach his/her post-secondary goal.
        ");

	$edit->addControl('', 'textarea')
		->sqlField('prcntevalrslts')
		->value($summaryText)
		->css('width', '100%')
		->css('height', '400px')
		->name('prcntevalrslts')
		->prepend("
            <b>5. A summary of the most recent evaluation/re-evaluation results" . $assessment_title . "</b><br>
            The following information is based upon the Evaluation Report dated: <u><i>" . $student->get('stdevaldt') . "</i>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('mo_formal')
		->css('width', '100%')
		->css('height', '100px')
		->name('mo_formal')
		->prepend("
            <b>6. A summary of formal and/or informal age appropriate transition assessments based on the student’s needs, preferences and interests (must be included no later than the first IEP to be in effect when the student turns age 16):</b>");

	if (IDEACore::disParam(63) != 'N') {
		$edit->addControl('', 'textarea')
			->sqlField('prsltsstateasmnts')
			->css('width', '100%')
			->css('height', '100px')
			->name('prsltsstateasmnts')
			->prepend("
                -general state (MAP/MAP-A):
            ");

		$edit->addControl('', 'textarea')
			->sqlField('mo_dwa')
			->css('width', '100%')
			->css('height', '100px')
			->name('mo_dwa')
			->prepend("
				-district-wide assessments
			");
	}

	$edit->addControl('', 'protected')
		->prepend("
            <b>7. For students participating in alternative assessments, a description of benchmarks or
            short-term objectives</b>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('mo_bench_pages')
		->css('width', '100%')
		->css('height', '100px')
		->name('mo_bench_pages')
		->prepend("
            - N/A Objectives/benchmarks are on goal page(s)
        ");

	$edit->addControl('', 'textarea')
		->sqlField('mo_bench_desc')
		->css('width', '100%')
		->css('height', '100px')
		->name('mo_bench_desc')
		->prepend("
            - Objectives/benchmarks described below
        ");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->setPresaveCallback('preSave', 'pl_plepmst.inc.php');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->firstCellWidth = "0%";
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->topButtons = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_plepmst')
			->setKeyField('prefid')
			->applyEditClassMode()
	);

	$button = new IDEABackup($tsRefID, 'webset.std_plepmst', 59, 'revertData');
	$editButton = $button->previewBackup();
	$edit->addButton($editButton);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

	FileUtils::getJSFile(SystemCore::$physicalRoot . '/apps/idea/__repository/system/js/autoText.js')->append();
?>

<script>
	function revertData(param) {
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./pl_plepmst_revert.ajax.php'),
			{
				'param': JSON.stringify(param)
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				for (var p in e.param.arr) {
					if ($('#' + p).length && p != 'lastuser' && p != 'lastupdate') {
						$('#' + p).val(e.param.arr[p]);
					}
				}
			}
		)
	}
</script>
