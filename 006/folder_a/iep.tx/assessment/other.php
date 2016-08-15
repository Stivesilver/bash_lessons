<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Review of Assessment Data - Other Information';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '45%';
	$edit->saveLocal = FALSE;

	$edit->addGroup('General Information');

	$saved = db::execSQL("
		SELECT *
		  FROM webset_tx.std_sam_other
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
	")->assoc();
	$items = explode(',', $saved['item']);
	$items_oth = array();
	$others = explode('|', $saved['item_addition']);
	for ($i = 0; $i < count($others); $i++) {
		if ($others[$i] != '') {			
			$pair = explode('::', $others[$i]);
			$items_oth[$pair[0]] = $pair[1];
		}		
	}

	$options = db::execSQL("
		SELECT refid,
			   validvalue,
			   validvalueid
		  FROM webset.glb_validvalues
		 WHERE valuename = 'TX_SAM_Oth'
		 ORDER BY valuename, sequence_number, validvalue ASC
	")->assocAll();

	for ($i = 0; $i < count($options); $i++) {
		$edit->addControl(
			FFSwitchYN::factory($options[$i]['validvalue'])
				->value(in_array($options[$i]['refid'], explode(',', $saved['item'])) ? 'Y' : '')
				
		)
			->name($options[$i]['refid']);
		
		if ($options[$i]['validvalueid'] == 'Y') {
			$id = 'oth_' . $options[$i]['refid'];
			$edit->addControl('Specify')
				->name($id)
				->value(isset($items_oth[$id]) ? $items_oth[$id] : '')
				->showIf($options[$i]['refid'], 'Y')
				->size(50);
			
		}
	}
	
	$edit->addControl('If Other, specify')
		->name('item_other')
		->value(isset($saved['item_other']) ? $saved['item_other'] : '')
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->name('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->name('iepyear');

	$edit->setPostsaveCallback('saveOther', 'other_save.inc.php');
	
    $edit->finishURL = 'javascript:parent.switchTab(4);';
	$edit->saveURL = CoreUtils::getURL('other_save.php', array('dskey' => $dskey));
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_other')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>