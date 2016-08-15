<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);

	$area_id = IDEAAppArea::TOTAL_MINUTES;
	$data = $student->getTotalSchoolHours();

	#Finds table ID
	$record = db::execSQL("
    	SELECT refid,
    	       int03
          FROM webset.std_general
         WHERE area_id = " . $area_id . "
           AND stdrefid = " . $tsRefID . "
    ")->assoc();

	$refid = (int)$record['refid'];

	$edit = new EditClass('edit1', $refid);

	$edit->title = 'Total School Hours';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('1. Assistive Technology')
			->data(array('N' =>'Not Required', 'Y' => 'Required: See Pg. 8'))
			->name('assistive_technology')
			->value($data['assistive_technology'])
			->req()
	);

	$edit->addControl(
		FFSwitchYN::factory('2. Applied (Voc.) Ed:')
			->data(array('Y' => 'Regular', 'N' =>'Special'))
			->emptyOption(true, 'N/A')
			->name('voc')
			->value($data['voc'])
	);

	$edit->addControl('Specify', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('voc_text')
		->showIf('voc', 'N')
		->value($data['voc_text']);

	$edit->addControl(
		FFSwitchYN::factory('3. Physical Education')
			->data(array('Y' => 'Regular', 'N' =>'Special'))
			->emptyOption(true, 'N/A')
			->name('physical')
			->value($data['physical'])
	);

	$edit->addControl('Specify', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('physical_text')
		->showIf('physical', 'N')
		->value($data['physical_text']);

	$edit->addControl(
		FFSwitchYN::factory('4. Transportation:')
			->data(array('Y' => 'Regular', 'N' =>'Special'))
			->emptyOption(true, 'N/A')
			->name('transportation')
			->value($data['transportation'])
	);

	$edit->addControl('Specify', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('transportation_text')
		->showIf('transportation', 'N')
		->value($data['transportation_text']);

	$edit->addControl('5. Length of School Day', 'text')
		->name('length_day')
		->value($data['length_day'])
		->req();

	$edit->addControl('6. Number of Days/Week', 'text')
		->name('number_day')
		->value($data['number_day'])
		->req();

	$edit->addControl('7. Length of School Year', 'text')
		->name('length_year')
		->value($data['length_year'])
		->req();

	$edit->addControl('8. Total School Hours/Week', 'text')
		->name('total_week')
		->value($data['total_week'])
		->req();

	$edit->addControl('9. Special Education Hours/Week', 'text')
		->name('special_week')
		->value($data['special_week'])
		->req();

	$edit->addControl('10. Hours per week the student will spend with children/students who do not have disabilities (time with non-disabled peers)', 'text')
		->name('hours_per_week')
		->value($data['hours_per_week'])
		->req();

	$edit->addControl(
		FFSwitchYN::factory('11. Since the last Annual Review, has the student participated in school sponsored extracurricular activities with non-disabled peers?')
			->name('since_peers')
			->value($data['since_peers'])
			->req()
	);

	$edit->addControl(
		FFSwitchYN::factory('12. Extended School Year Services')
			->data(array('Y' => 'Required: See service delivery grid above or an Additional page 11 for services to be provided', 'N' =>'Required: Continue to implement current IEP'))
			->emptyOption(true, 'Not Required')
			->name('extended_services')
			->value($data['extended_services'])
			->breakRow()
	);

	$edit->addControl(
		FFSwitchYN::factory('13. a) The extent, if any, to which the student will not participate in regular classes:'))
			->data(array('Y' => 'Not Applicable: Student will participate fully'))
			->emptyOption(true, 'None Selected')
			->name('extent')
			->help('The extent, if any, to which the student will not participate in regular classes and in extracurricular and other nonacademic activities, including lunch, recess, transportation, etc., with students who do not have disabilities:')
			->value($data['extent'])
			->breakRow();

	$edit->addControl('Students who do not have disabilities', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('extent_explan')
		->showIf('extent', '')
		->value($data['extent_explan']);

	$edit->addControl(
		FFSwitchYN::factory('b) If the IEP requires any removal of the student from the school :'))
			->data(array('Y' => 'Not Applicable: Student will participate fully', 'N' => 'The IEP requires removal of the student from the regular education environment because: (provide a detailed explanation - use additional pages if necessary)'))
			->emptyOption(true, 'None Selected')
			->name('removal')
			->help('If the IEP requires any removal of the student from the school, classroom, extracurricular, or nonacademic activities, (e.g., lunch, recess, transportation, etc.) that s/he would attend if not disabled, the PPT must justify this removal from the regular education environment.')
			->value($data['removal'])
			->breakRow();

	$edit->addControl('Provide a detailed explanation', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('removal_explan')
		->help('The IEP requires removal of the student from the regular education environment because: (provide a detailed explanation - use additional pages if necessary)')
		->showIf('removal', 'N')
		->value($data['removal_explan']);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id')->name('area_id');

	$edit->setPostsaveCallback('saveData', 'total_school_hours_edit.inc.php');
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->firstCellWidth = "50%";
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

	$message = "<i>The LRE Checklist (ED632) must be completed and attached to this IEP if the student is to be removed from the regular education environment for 60% or more of the time. It is recommended that the LRE Checklist be utilized when making any placement decision to ensure conformity with the LRE provisions of the Individuals with Disabilities Education Act.</i>";

	print UIMessage::factory($message, UIMessage::NOTE)
		->textAlign('left')
		->toHTML();

?>
