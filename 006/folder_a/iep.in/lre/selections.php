<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$student = IDEAStudent::factory($tsRefID);

	if ($student->get('ecflag') == 'Y') {
		$ectext = 'This student has been established as an EC (Early Childhood) student. If this is not correct you may change this under the EC option under Student Manager Main Screen';
	} else {
		$ectext = 'This student has been established as <u><i><b>NOT</b></i></u> an EC (Early Childhood) student. If this is not correct you may change this under the EC option under Student Manager Main Screen.';
	}

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'LRE Selections';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset.std_in_lre_selections', 'stdrefid');

	$edit->addGroup('General Information');	
	$edit->addControl('Select appropriate', 'select_radio')
		->sqlField('silcrefid')
		->sql("
			SELECT silcrefid,
			       COALESCE(silclrecode, '')  ||  COALESCE(' - ' || silcmaindesc, '') || COALESCE(' - ' || silcdesc, '')
			  FROM webset.statedef_in_lre_codes
		     WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND silcearlychildhoodsw = '" . $student->get('ecflag') . "'
			 ORDER BY silclrecode
		")
		->breakRow()
		->req();
	
	$edit->addControl('', 'protected')
		->append(UIMessage::factory($ectext, UIMessage::NOTE)->toHTML());

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	
	$edit->finishURL = 'javascript:parent.switchTab(1);';
	$edit->cancelURL = 'javascript:parent.switchTab();';
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_lre_selections')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>