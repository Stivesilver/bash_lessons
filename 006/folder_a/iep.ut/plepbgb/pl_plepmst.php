<?php

	Security::init();

	$dskey = io::get('dskey');

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
	$prefid = db::execSQL($SQL)->getOne();

	#Starts EditClass Page
	$edit = new EditClass('edit1', (int)$prefid);

	$edit->title = 'Present Levels of Academic Achievement and Functional Performance';

	$edit->setSourceTable('webset.std_plepmst', 'prefid');

	$edit->addGroup('General Information');

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('pleadstat')
		->value($student->get('stdnamefml') . ", age " . $student->get('stdage') . ", grade " . $student->get('grdlevel'))
		->css('width', '100%')
		->css('height', '90px')
		->name('pleadstat')
		->prepend("
            <b>Provide Brief Lead-in Statement, which includes:</b> Student's Name, Age, and Grade
        ");

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('pmtsgened')
		->css('width', '100%')
		->css('height', '90px')
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
		->autoHeight(true)
		->sqlField('pbaseline')
		->css('width', '100%')
		->css('height', '90px')
		->name('pbaseline')
		->prepend("
            <b>2. The strengths of the child</b> <br/>
            For students with transition plans, consider how the strengths of the child relate to the
            child's post-secondary goals.
        ");

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('pgdconcrn')
		->css('width', '100%')
		->css('height', '90px')
		->name('pgdconcrn')
		->prepend("
            <b>3. Concerns of the parent/guardian for enhancing the education of the child</b> <br>
            For students with transition plans, consider the parent/guardian's expectations for the child after the
            child leaves high school.
        ");

	$edit->addControl('', 'protected')
		->prepend("
            <b>4. Summary of the child's performance and recent assessments</b>
        ");

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('mo_formal')
		->css('width', '100%')
		->css('height', '60px')
		->name('mo_formal');

	/*$edit->addControl('', 'protected')
		->prepend("
            <b>5. For students participating in alternative assessments, a description of benchmarks or
            short-term objectives</b>
        ");

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('mo_bench_pages')
		->css('width', '100%')
		->css('height', '60px')
		->name('mo_bench_pages')
		->prepend("
            - N/A Objectives/benchmarks are on goal page(s)
        ");

	$edit->addControl('', 'textarea')
		->autoHeight(true)
		->sqlField('mo_bench_desc')
		->css('width', '100%')
		->css('height', '60px')
		->name('mo_bench_desc')
		->prepend("
            - Objectives/benchmarks described below
        ");
	*/

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
					var selector = $('#' + p);
					if (selector.length && p != 'lastuser' && p != 'lastupdate') {
						selector.val(e.param.arr[p]);
					}
				}
			}
		);
	}
</script>
