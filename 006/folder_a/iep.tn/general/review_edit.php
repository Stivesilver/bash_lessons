<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Review/Changes';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup("General Information");

	$edit->addControl(FFIDEAValidValues::factory('TN_BGB_Review'))
		->caption('Review Status Key')
		->maxRecords(1)
		->sqlField('int01');
	$edit->addControl('Comment', 'textarea')->sqlField('txt02');
	$edit->addControl('Date', 'date')->sqlField('dat01');


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value(IDEAAppArea::TN_IFSP_OUTCOME_ACTION)->sqlField('area_id');

	$edit->finishURL = CoreUtils::getURL('review_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('review_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
