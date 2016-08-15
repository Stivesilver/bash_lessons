<?php

	Security::init();

	$refid = io::geti('RefID');

	$dis = db::execSQL("
		SELECT fb_type
          FROM webset.disdef_forms
         WHERE dfrefid = $refid
	")->getOne();

	$edit = new EditClass('edit1', $refid);
	$edit->title = 'Add/Edit Form Template';

	if ($dis == 2) {
		$edit->disabled(true);
	}

	$edit->setSourceTable('webset.disdef_forms', 'dfrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("Form Purpose"))
		->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")->sqlField('mfcprefid');

	$edit->addControl('Form Title', 'text')
		->width('400px')
		->req()
		->sqlField('title');

	$edit->addControl('state', 'hidden')
		->value(SystemCore::$VndRefID)
		->sqlField('vndrefid');

	$edit->addControl('type', 'hidden')
		->value(1)
		->sqlField('fb_type');

	$fb = FFFormBuilder::factory()
		->settings(FBIDEASettings::factory());
	if ($dis == 2) {
		$fb->displayClearButton(false)
			->displayEditButton(false);
	}

	$edit->addControl($fb)
		->caption('Form Template')
		->sqlField('fb_content');

	$edit->addUpdateInformation();

	$edit->saveAndEdit = true;

	$edit->printEdit();

	if ($dis == 2) {
		print UIMessage::factory("You cannot edit this form because the form has been downloaded from Warehouse", UIMessage::NOTE)
			->textAlign('left')
			->toHTML();
	}
?>
