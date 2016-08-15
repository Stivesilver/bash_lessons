<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');

	$arr_agree = array('Y' => 'Agree', 'N' => 'Disagree');

	$edit = new EditClass('edit1', $evalproc_id);

	$edit->title = 'SLD Members';

	$edit->setSourceTable('webset.es_std_er_participants_sld', 'eprefid');

	$edit->addGroup('Regular Education Professional');

	$edit->addControl(FFCheckBox::factory('Regular Education Professional'))
		->baseValue('Y')
		->sqlField('regular_prof_sw');

	$edit->addControl('Name')
		->css('width', '70%')
		->sqlField('regular_prof_namerole');

	$edit->addControl(FFCheckBox::factory('Child\'s Regular Education Teacher'))
		->baseValue('Y')
		->sqlField('regular_edu_teacher');

	$edit->addControl(FFCheckBox::factory('If the Child does not have a Regular Education Teacher, a regular classroom teacher qualified to teach a child of his/her age'))
		->baseValue('Y')
		->sqlField('regular_edu_classroom');

	$edit->addControl(FFCheckBox::factory('For a Child less than school age, an individual qualified to teach a child of that age'))
		->baseValue('Y')
		->sqlField('regular_edu_ind');

	$edit->addControl(FFIDEASwitchYN::factory('Agreement'))
		->data($arr_agree)
		->sqlField('regular_prof_agree');

	$edit->addControl('Initials (if no signature)')
		->sqlField('edu_initials');

	$edit->addGroup('Assessment Professional');

	$edit->addControl(FFCheckBox::factory('Assessment Professional'))
		->baseValue('Y')
		->sqlField('assess_prof_sw');

	$edit->addControl('Name')
		->css('width', '70%')
		->sqlField('assess_prof_namerole');

	$edit->addControl(FFIDEASwitchYN::factory('Agreement'))
		->data($arr_agree)
		->sqlField('assess_prof_agree');

	$edit->addControl('Initials (if no signature)')
		->sqlField('prof_initials');

	$edit->addGroup('Additional Qualified Professionals');

	$edit->addControl(FFCheckBox::factory('Additional Qualified Professionals'))
		->baseValue('Y')
		->sqlField('assess_qual_sw');

	$edit->addControl('Name')
		->css('width', '70%')
		->sqlField('assess_qual_namerole');

	$edit->addControl('Role')
		->css('width', '70%')
		->sqlField('assess_qual_role');

	$edit->addControl(FFIDEASwitchYN::factory('Agreement'))
		->data($arr_agree)
		->sqlField('assess_qual_agree');

	$edit->addControl('Initials (if no signature)')
		->sqlField('qual_initials');

	$edit->addControl('Name')
		->css('width', '70%')
		->sqlField('assess_qual_namerole_sec');

	$edit->addControl('Role')
			->css('width', '70%')
		->sqlField('assess_qual_role_sec');

	$edit->addControl(FFIDEASwitchYN::factory('Agreement'))
		->data($arr_agree)
		->sqlField('assess_qual_agree_sec');

	$edit->addControl('Initials (if no signature)')
		->sqlField('qual_initials_sec');

	$edit->addUpdateInformation();
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->topButtons = true;
	$edit->finishURL = 'javascript:parent.switchTab();';
	$edit->cancelURL = CoreUtils::getURL('', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '40%';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_er_participants_sld')
			->setKeyField('eprefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
