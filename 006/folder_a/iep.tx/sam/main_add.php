<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Texas Assessment Program';

	$edit->setSourceTable('webset_tx.std_sam_main', 'samrefid');

	$edit->addGroup('General Information');
	$edit->addControl(
		FFIDEASchoolYear::factory()
			->sqlField('syrefid')
			->caption('School Year')
	);	  
	
	$edit->addControl('Date', 'date')
		->sqlField('begdate');
	
	$edit->addControl('Description')
		->sqlField('samdesc')
		->size(80);
	
	$edit->addControl(
		FFGradeLevel::factory('')
			->sqlField('grade_id')
			->caption('Enrollment grade at testing')
	);
	
	$edit->addControl(
		FFSwitchYN::factory('Include in ARD report')
			->sqlField('ardinclude')
	);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	
	$edit->finishURL = CoreUtils::getURL('main.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('main.php', array('dskey' => $dskey));

	$edit->printEdit();
?>