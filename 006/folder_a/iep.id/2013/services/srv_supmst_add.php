<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);
	$stdIEPYear = $ds->safeGet('stdIEPYear');	
	$servId = 'stsrefid';

	$servSQL = "
		SELECT stsrefid, COALESCE(stscode || ' - ' , '') || stsdesc
		  FROM webset.statedef_services_sup
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND (recdeactivationdt>now() or recdeactivationdt is Null)
		 ORDER BY CASE lower(stsdesc) WHEN 'other' THEN 'z' ELSE stsdesc END
    ";

	$naswSQL = "
		SELECT stsrefid
		  FROM webset.statedef_services_sup
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND nasw = 'Y'
    ";

	$id_na = db::execSQL($naswSQL)->indexAll();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Special Education Services';

	$edit->setSourceTable('webset.std_srv_sup', 'ssmrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Service', 'select')
		->sqlField($servId)
		->name($servId)
		->sql($servSQL)
		->emptyOption(true)
		->req();
	
	$edit->addControl('Position Responsible')
		->sqlField('ssmteacherother')
		->hideIf($servId, $id_na)
		->size(50);
	
	$edit->addControl('Start Date', 'date')
		->sqlField('ssmbegdate')
		->value($student->getDate('stdenrolldt'))
		->hideIf($servId, $id_na);

	$edit->addControl('Duaration (Ending Date)', 'date')
		->sqlField('ssmenddate')
		->value($student->getDate('stdcmpltdt'))
		->hideIf($servId, $id_na);


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('srv_supmst.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_supmst.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
