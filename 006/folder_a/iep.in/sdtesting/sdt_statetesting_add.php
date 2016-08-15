<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit State Testing';

	$edit->setSourceTable('webset.std_in_test_state', 'sitsrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Test', 'select')
		->sqlField('sitdrefid')
		->name('sitdrefid')
		->sql("
			SELECT t1.sitdrefid,
				   t0.sitmdesc || '->' || t1.sitddesc
			  FROM webset.statedef_in_testmst AS t0
				   INNER JOIN webset.statedef_in_testdtl AS t1 ON t1.sitmrefid = t0.sitmrefid
			 ORDER BY t1.sitmrefid, t1.sitdrefid
		")
		->emptyOption(TRUE)
		->req();
	
	$edit->addControl('Date', 'date')
		->sqlField('sitsdt');

	$edit->addControl('Score')
		->sqlField('sitsscore')
		->size(25)
		->req();
	
	$edit->addControl('Rating', 'select')
		->sqlField('sitrrefid')
		->name('sitrrefid')
		->sql("
			SELECT sitrrefid,
				   sitrdesc
			  FROM webset.statedef_in_test_rating
			 ORDER BY sitrrefid
		")
		->emptyOption(TRUE)
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('sdt_statetesting.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('sdt_statetesting.php', array('dskey' => $dskey));

	$edit->printEdit();
?>