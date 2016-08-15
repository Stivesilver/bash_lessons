<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode', TRUE);
	$title = ($mode == 'F' ? 'FBA' : 'BIP');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit ' . $title . ' - Items';
	
	$edit->firstCellWidth = '30%';

	$edit->setSourceTable('webset.std_in_bipitems', 'recrefid');


	if (io::get('RefID') == 0) {
		$edit->topButtons = true;
		$sql = "
			SELECT birefid, 
			       bcdesc || ' -> ' || bidesc
			  FROM webset.disdef_bipitems items
			       INNER JOIN webset.disdef_bipcat cat ON items.bcrefid = cat.bcrefid
			 WHERE cat.vndrefid = VNDREFID
               AND birefid NOT IN (
							SELECT birefid
							  FROM webset.std_in_bipitems 
							 WHERE stdrefid = " . $tsRefID . "
			   )
			   AND form_type = '" . $mode . "'
	   	     ORDER BY bcseq, bcdesc, biseq, bidesc
		";
	} else {
		$sql = "
			SELECT birefid, 
			       bcdesc || ' -> ' || bidesc
			  FROM webset.disdef_bipitems items
			       INNER JOIN webset.disdef_bipcat cat ON items.bcrefid = cat.bcrefid
			 WHERE cat.vndrefid = VNDREFID
               AND birefid IN (
					SELECT birefid 
					  FROM webset.std_in_bipitems 
					 WHERE recrefid = " . io::get('RefID') . "
			   )
	   	     ORDER BY bcseq, bcdesc, biseq, bidesc
		";
	}

	$edit->addGroup('General Information');

	$edit->addControl($title . ' - Item', 'select_radio')
		->sqlField('birefid')
		->name('birefid')
		->sql($sql)
		->breakRow()
		->req();

	$edit->addControl('Narrative')
		->sqlField('rectext')
		->name('rectext')
		->showIf('birefid', db::execSQL("
			SELECT birefid
			  FROM webset.disdef_bipitems
		     WHERE bistat = 'Y'
			")->indexAll()
		)
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Record Value', 'hidden')->value('Y')->sqlField('recval');

	$edit->finishURL = CoreUtils::getURL('items.php', array('dskey' => $dskey, 'mode' => $mode));
	$edit->cancelURL = CoreUtils::getURL('items.php', array('dskey' => $dskey, 'mode' => $mode));

	$edit->printEdit();
?>