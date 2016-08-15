<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Determined Needs';

	$edit->setSourceTable('webset.std_in_dneeds', 'dnrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Order #', 'integer')
		->sqlField('dnseq')
		->value(
			(int) db::execSQL("
					SELECT max(dnseq)
					  FROM webset.std_in_dneeds
					 WHERE stdrefid = " . $tsRefID . "
	            ")->getOne() + 1
		)
		->size(20);

	$edit->addControl('Narrative', 'textarea')
		->sqlField('dnnarr')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('dn_dneeds.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('dn_dneeds.php', array('dskey' => $dskey));

	$edit->printEdit();
?>