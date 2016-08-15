<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$helpButon = FFMenuButton::factory('Populate');

	$text = db::execSQL("
		SELECT sinarr
		  FROM webset.std_in_supp_inf std
			   INNER JOIN webset.statedef_supp_inf_cat state ON state.sicrefid = std.sicrefid
		 WHERE stdrefid = " . $tsRefID . "
		   AND state.sicrefid = 10
		 ORDER BY sirefid
	")->getOne();

	if ($text != '') {
		$helpButon->addItem($text, '$("#medication").val($.trim($("#medication").val() + " " + ' . json_encode($text) . '))');
	}

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'BIP - General Part';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset.std_in_bipgen', 'stdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Medication(s)', 'textarea')
		->sqlField('medication')
		->name('medication')
		->css('width', '100%')
		->css('height', '50px')
		->append(
			($text == '' ? '' : $helpButon)
		);

	$edit->addControl('Review Date 1', 'date')->sqlField('review1');
	$edit->addControl('Review Date 2', 'date')->sqlField('review2');
	$edit->addControl('Review Date 3', 'date')->sqlField('review3');
	$edit->addControl('Review Date 4', 'date')->sqlField('review4');
	$edit->addControl('Review Date 5', 'date')->sqlField('review5');
	$edit->addControl('Review Date 6', 'date')->sqlField('review6');
	$edit->addControl('Review Date 7', 'date')->sqlField('review7');
	$edit->addControl('Review Date 8', 'date')->sqlField('review8');
	$edit->addControl('Review Date 9', 'date')->sqlField('review9');
	$edit->addControl('Review Date 10', 'date')->sqlField('review10');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_bipgen')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>