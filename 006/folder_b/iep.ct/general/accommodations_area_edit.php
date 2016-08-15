<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudentCT($tsRefID);

	$edit = new EditClass('', io::geti('RefID'));

	$edit->title = 'Edit\Add Program Accommodations and Modifications';

	$edit->setSourceTable('webset.std_srv_progmod', 'ssmrefid');

	$areas = db::execSQL("
			SELECT macrefid,
                   macdesc,
                   seq_num,
                   CASE WHEN NOW() > enddate THEN 'In-Active' ELSE 'Active' END as status
              FROM webset.statedef_mod_acc_cat
			 WHERE screfid = " . VNDState::factory()->id . " AND ( ((CASE enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') )
			")->assocAll();
	# select list with areas
	$data = array();

	foreach ($areas as $item) {
		$key = $item['macrefid'];

		$data[$key] = $item['macdesc'];
	}

	$edit->addGroup('General Information');
	$edit->addControl('Accommodations', 'select')
		->sqlField('malrefid')
		->data($data);

	$edit->addControl('Description', 'textarea')
		->sqlField('ssmmbrother');

	$edit->addControl('Site/Activities', 'textarea')
		->sqlField('ssmteacherother');

	$edit->addControl('Begin Date', 'date')
		->value($student->getDate('stdenrolldt'))
		->sqlField('ssmbegdate');

	$edit->addControl('End Date', 'date')
		->value($student->getDate('stdcmpltdt'))
		->sqlField('ssmenddate');

	$edit->addControl('', 'hidden')
		->sqlField('stdrefid')
		->value($tsRefID);

	$edit->addControl('', 'hidden')
		->sqlField('iepyear')
		->value($stdIEPYear);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->addUpdateInformation();
	$edit->printEdit();

?>
