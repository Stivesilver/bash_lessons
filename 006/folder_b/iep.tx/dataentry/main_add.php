<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudentTX::factory($tsRefID);

	$edit = new EditClass('edit1', io::get('RefID'));

	$disabilities = $student->getDisability();
	$placements = $student->getPlacement();
	$rad = $student->getRad();
	$guardians = array_map(create_function('$a', 'return $a["gdlnm"] . " " . $a["gdfnm"];'), $student->getGuardians());
	$guard_phn = array_map(create_function('$a', 'return $a["gdwphn"];'), $student->getGuardians());

	$lre = array_map(create_function('$a', 'return $a["spccode"] . " - " . $a["spcdesc"];'), $student->getPlacement());
	
	$a = $student->getMeetPurposes();
	$b = $student->getMeetPurposesSelected();
	$meetpurpose = array();
	for ($i = 0; $i < count($a); $i++) {
		if (in_array($a[$i]['refid'], explode(',', $b['type_report']))) {
			$meetpurpose[] = $a[$i]['adesc'];
		}
	}
	
	$services = array_map(create_function('$a', 'return $a["service"];'), $student->getRelatedServices());
	
	$edit->title = 'Add/Edit Data Entry Form';
	$edit->topButtons = TRUE;

	$edit->setSourceTable('webset_tx.std_dataentry', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('Name')
		->sqlField('stdname')
		->value($student->get('stdname'))
		->size(40);

	$edit->addControl('Student ID')
		->sqlField('stdid')
		->value($student->get('stdschid'))
		->size(40);

	$edit->addControl('Student ID')
		->sqlField('ssn')
		->value($student->get('stdfedidnmbr'))
		->size(40);

	$edit->addControl('District')
		->sqlField('district')
		->value(SystemCore::$VndName)
		->size(40);

	$edit->addControl('Campus')
		->sqlField('campus')
		->value($student->get('vouname'))
		->size(40);

	$edit->addControl('Grade Level')
		->sqlField('grade')
		->value(strval($student->get('grdlevel')))
		->size(40);

	$edit->addControl('DOB')
		->sqlField('dob')
		->value($student->get('stddob'))
		->size(40);

	$edit->addControl('Sex')
		->sqlField('sex')
		->value($student->get('stdsex'))
		->size(40);

	$edit->addControl('Ethnicity')
		->sqlField('ethnicity')
		->value($student->get('ethcode'))
		->size(40);

	$edit->addControl('Language')
		->sqlField('language')
		->value($student->get('prim_lang'))
		->size(40);

	$edit->addControl('Primary (HC) Handicapp')
		->sqlField('hc1')
		->value(isset($disabilities[0]['code']) ? $disabilities[0]['code'] : '')
		->size(40);

	$edit->addControl('Second HC')
		->sqlField('hc2')
		->value(isset($disabilities[1]['code']) ? $disabilities[1]['code'] : '')
		->size(40);

	$edit->addControl('Third HC')
		->sqlField('hc3')
		->value(isset($disabilities[2]['code']) ? $disabilities[2]['code'] : '')
		->size(40);

	$edit->addControl('Multi HC')
		->sqlField('hcm')
		->value(isset($disabilities[3]['code']) ? $disabilities[3]['code'] : '')
		->size(40);

	$edit->addControl('Speech')
		->sqlField('speech')
		->size(80);

	$edit->addControl('Instructional Arrangement')
		->sqlField('lre')
		->value(implode(', ', $lre))
		->size(80);

	$edit->addControl(FFSwitchYN::factory('ESY'))
		->sqlField('esy')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'ESY Services_chk', $stdIEPYear));

	$edit->addControl('Last FIE')
		->sqlField('lastfie')
		->value($student->get('stdevaldt'));

	$edit->addControl('Last Annual Review')
		->sqlField('lastannual')
		->value($student->get('stdiepmeetingdt'));

	$edit->addControl('Last Long ARD')
		->sqlField('longard')
		->value(isset($rad['longard']) ? CoreUtils::formatDate($rad['longard'], 'm/d/Y') : '');

	$edit->addControl('Entry Date')
		->sqlField('entrydate');

	$edit->addControl('Active')
		->sqlField('activestd')
		->value($student->get('stdstatus') == 'Y' ? 'Yes' : 'No');

	$edit->addControl('Date Of Dismissal')
		->sqlField('dismissal')
		->value(isset($rad['stdexitdt']) ? CoreUtils::formatDate($rad['stdexitdt'], 'm/d/Y') : '');

	$edit->addControl('Exit Reason Code')
		->sqlField('dismissal')
		->value(isset($rad['exitreason']) ? $rad['exitreason'] : '');

	$edit->addControl('Date Of ARD')
		->sqlField('dateofard')
		->value(CoreUtils::formatDate($student->get('stdiepmeetingdt'), 'm/d/Y') . ' ' . CoreUtils::formatDate($rad["briefard"], 'm/d/Y') . ' ' . CoreUtils::formatDate($rad["amendment"], 'm/d/Y'))
		->size(50);

	$edit->addControl('Purpose of ARD')
		->sqlField('purposeofard')
		->value(implode(', ', $meetpurpose))
		->size(50);

	$edit->addControl('Services', 'textarea')
		->sqlField('services')
		->value(implode(', ', $services))
		->css('width', '100%')
		->css('height', '50px');
	
	$edit->addControl('Parent/Guardian')
		->sqlField('parent')
		->value(isset($guardians[0]) ? $guardians[0] : '')
		->size(50);
	
	$edit->addControl('Other Guardian')
		->sqlField('otherparent')
		->value(isset($guardians[1]) ? $guardians[1] : '')
		->size(50);
	
	$edit->addControl('Address')
		->sqlField('address')
		->value($student->get('stdhadr1'))
		->size(50);
	
	$edit->addControl('City')
		->sqlField('city')
		->value($student->get('stdhcity'));
	
	$edit->addControl('State')
		->sqlField('state')
		->value($student->get('stdhstate'));
	
	$edit->addControl('Zip')
		->sqlField('zip')
		->value($student->get('stdhzip'));
	
	$edit->addControl('Home Phone')
		->sqlField('homephone')
		->value($student->get('stdhphn'));
	
	$edit->addControl('Work Phone')
		->sqlField('wirkphone')
		->value(isset($guard_phn[0]) ? $guard_phn[0] : '');
	
	$edit->addControl('Comments', 'textarea')
		->sqlField('comments')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('main.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('main.php', array('dskey' => $dskey));

	$edit->printEdit();
?>