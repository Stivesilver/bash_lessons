<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$lastuser = '';
	$lastupdate = '';

	$edit = new EditClass("edit1", $tsRefID);

	$edit->title = 'FBA - Intake Info';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

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
		$helpButon->addItem($text, '$("#medic").val($.trim($("#medic").val() + " " + ' . json_encode($text) . '))');
	}

	$texts = db::execSQL("
		SELECT idesc,
			   irefid,
			   std.stdrefid,
			   std.item_text,
			   std.tr_item_id,
			   std.lastupdate,
			   std.lastuser
		  FROM webset.statedef_in_fbp_intakes state 
			   LEFT OUTER JOIN webset.std_in_fbp_intakes std ON std.tr_item_id = state.irefid AND stdrefid = " . $tsRefID . "		 
		 ORDER BY sequence
	")->assocAll();

	$dates = db::execSQL("
		SELECT imrefid,
			   medic,
			   revdt1,
			   revdt2,
			   revdt3,
			   revdt4,
			   revdt5,
			   revdt6,
			   revdt7,
			   revdt8,
			   revdt9,
			   revdt10
		  FROM webset.std_in_fbp_intakes_med
		 WHERE stdrefid = " . $tsRefID . "
	")->assoc();

	$edit->addGroup('General Information');

	for ($i = 0; $i < count($texts); $i++) {
		$edit->addControl($texts[$i]['idesc'], 'textarea')
			->name($texts[$i]['irefid'])
			->value($texts[$i]['item_text'])
			->css('width', '100%')
			->css('height', '50px');
		$lastupdate = $texts[$i]['lastupdate'];
		$lastuser = $texts[$i]['lastuser'];
	}

	$edit->addControl('Medication(s)', 'textarea')
		->name('medic')
		->value($dates['medic'])
		->css('width', '100%')
		->css('height', '50px')
		->append(
			($text == '' ? '' : $helpButon)
	);

	$edit->addControl('Review Date 1', 'date')->value($dates['revdt1'])->name('revdt1');
	$edit->addControl('Review Date 2', 'date')->value($dates['revdt2'])->name('revdt2');
	$edit->addControl('Review Date 3', 'date')->value($dates['revdt3'])->name('revdt3');
	$edit->addControl('Review Date 4', 'date')->value($dates['revdt4'])->name('revdt4');
	$edit->addControl('Review Date 5', 'date')->value($dates['revdt5'])->name('revdt5');
	$edit->addControl('Review Date 6', 'date')->value($dates['revdt6'])->name('revdt6');
	$edit->addControl('Review Date 7', 'date')->value($dates['revdt7'])->name('revdt7');
	$edit->addControl('Review Date 8', 'date')->value($dates['revdt8'])->name('revdt8');
	$edit->addControl('Review Date 9', 'date')->value($dates['revdt9'])->name('revdt9');
	$edit->addControl('Review Date 10', 'date')->value($dates['revdt10'])->name('revdt10');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($lastuser);
	$edit->addControl('Last Update', 'protected')->value($lastupdate);
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('intake_save.php', array('dskey' => $dskey));
	$edit->saveURL = CoreUtils::getURL('intake_save.php', array('dskey' => $dskey));
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_fbp_intakes_med')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>