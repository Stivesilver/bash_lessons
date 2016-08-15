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
	$SQL = "SELECT prefid
              FROM webset.std_plepmst
             WHERE stdrefid = " . $tsRefID;

	$result = db::execSQL($SQL);
	if (!$result->EOF) {
		$prefid = $result->fields[0];
	} else {
		$prefid = 0;
	}

	#Initialises default summary text
	$ownText = IDEACore::disParam(109);
	if ($ownText != "") {
		$summaryText = $ownText;
	} else {
		$summaryText = "Physical Condition / Motor:" . chr(13) . chr(13) . chr(13) .
			"Speech and Language:" . chr(13) . chr(13) . chr(13) .
			"Intellectual / Cognitive Development / Adaptive Behavior:" . chr(13) . chr(13) . chr(13) .
			"Achievement:" . chr(13) . chr(13) . chr(13) .
			"Social / Emotional / Behavioral Functioning:";
	}

	#Starts EditClass Page
	$edit = new EditClass('edit1', $prefid);

	$edit->title = 'Present Levels of Academic Achievement and Functional Performance';

	$edit->setSourceTable('webset.std_plepmst', 'prefid');

	$edit->addGroup('General Information');

	$edit->addControl('', 'textarea')
		->sqlField('pbaseline')
		->name('pbaseline')
		->css('width', '100%')
		->css('height', '90px')
		->prepend("
            <b>1. Describe the child's strengths</b>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pmtsgened')
		->name('pmtsgened')
		->css('width', '100%')
		->css('height', '90px')
		->prepend("
            <b>2. Explain how the child's exceptionality affects his/her classroom performance</b>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pgdconcrn')
		->name('pgdconcrn')
		->css('width', '100%')
		->css('height', '90px')
		->prepend("
            <b>3. Indicate parental concerns</b>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('pstrstd')
		->name('pstrstd')
		->css('width', '100%')
		->css('height', '90px')
		->prepend("
            <b>4. Indicate educational changes since the last IEP</b>
        ");

	$edit->addControl('', 'textarea')
		->sqlField('prcntevalrslts')
		->name('prcntevalrslts')
		->value($summaryText)
		->css('width', '100%')
		->css('height', '250px')
		->prepend("
            <b>5. Summarize the most recent evaluation</b>
        ");

	$edit->addControl('', 'protected')
		->prepend("
            <b>6. Summarize the results of the most recent state and district assessments</b>
        ");

	if (IDEACore::disParam(63) != 'N') {
		$edit->addControl('', 'textarea')
			->sqlField('prsltsstateasmnts')
			->name('prsltsstateasmnts')
			->css('width', '100%')
			->css('height', '60px')
			->prepend("
                - general state:
            ");
	}

	$edit->addControl('', 'textarea')
		->sqlField('mo_dwa')
		->name('mo_dwa')
		->css('width', '100%')
		->css('height', '60px')
		->prepend("
            - district-wide assessments:
        ");

	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->setPresaveCallback('preSave', 'pl_plepmst.inc.php');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
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
	
	$button = new IDEABackup($tsRefID, 'webset.std_plepmst', 60, 'revertData');
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
					if ($('#' + p).length && p != 'lastuser' && p != 'lastupdate') {
						$('#' + p).val(e.param.arr[p]);
					}
				}
			}
		)
	}
</script>
