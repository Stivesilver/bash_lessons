<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit District Testing';

	$edit->setSourceTable('webset.std_in_test_dis', 'sitdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Test', 'select')
		->sqlField('ditdrefid')
		->name('ditdrefid')
		->sql("
			SELECT t1.ditdrefid,
				   t0.ditmdesc || '->' || t1.ditddesc || ': ' || COALESCE(t1.ditdscore, '')
			  FROM webset.disdef_in_testmst AS t0
				   INNER JOIN webset.disdef_in_testdtl AS t1 ON t1.ditmrefid = t0.ditmrefid
			 WHERE t0.vndrefid = VNDREFID
			   AND t1.vndrefid = VNDREFID
			   AND (t0.enddate IS NULL or now()< t0.enddate)
			   AND (t1.enddate IS NULL or now()< t1.enddate)
			 ORDER BY t1.ditmrefid, t1.ditdrefid
		")
		->emptyOption(TRUE)
		->req();
	
	$edit->addControl('Date', 'date')
		->sqlField('sitddt');

	$edit->addControl('Score')
		->sqlField('sitdscore')
		->size(25)
		->req();
	
	$edit->addControl('Rating', 'select')
		->sqlField('ditrrefid')
		->name('ditrrefid')
		->sql("
			SELECT ditrrefid,
				   ditrdesc
			  FROM webset.disdef_in_test_rating
			 WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
	           AND vndrefid = VNDREFID
			 ORDER BY ditrrefid
		")
		->emptyOption(TRUE)
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('sdt_distesting.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('sdt_distesting.php', array('dskey' => $dskey));

	$edit->printEdit();
?>