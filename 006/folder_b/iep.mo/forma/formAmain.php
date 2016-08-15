<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$SQL = "
        SELECT refid
            FROM webset.std_form_a
           WHERE stdrefid = " . $tsRefID . "
             AND syrefid  = " . $stdIEPYear . "
    ";

	$RefID = (int)db::execSQL($SQL)->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Form A: Blind and Visually Impaired';

	$edit->setSourceTable('webset.std_form_a', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('IEP team has determined the following regarding Braille/Braille instruction', 'textarea')
		->sqlField('brinst')
		->css('width', '100%');

	$edit->addControl('', 'select_radio')
		->name('bransw')
		->sqlField('bransw')
		->data(array(
				'N' => 'No, The student does not need Braille/Braille instruction.',
				'Y' => 'Yes, the student needs Braille/Braille instruction. Appropriate goals and benchmarks/objectives, which specify the competencies in reading and writing Braille to be taught during the school year, are included in this IEP. If yes, complete items below.')
		)
		->breakRow();

	$edit->addControl('If no, complete the following. The IEP team made the determination that Braille instruction is not appropriate for this child based upon the following factors', 'textarea')
		->sqlField('factors')
		->css('width', '100%');

	$edit->addControl('Methods by which Braille will be integrated into normal classroom activities', 'textarea')
		->sqlField('methods')
		->css('width', '100%');

	$edit->addControl('Date on which Braille instruction will begin', 'date')
		->sqlField('brbegdt');

	$edit->addControl('Duration of each session')
		->sqlField('duration')
		->size(40);

	$edit->addControl('Level of competency in Braille reading and writing expected to be achieved by the end of the period covered in this IEP', 'textarea')
		->sqlField('brlevel')
		->css('width', '100%');

	$edit->addControl('Rehabilitation Services', 'select_check')
		->name('discussed')
		->sqlField('discussed')
		->data(array(
				'Y' => 'A referral to Rehabilitation Services for the blind has been discussed with the parent.',
			)
		)
		->displaySelectAllButton(false);

	$edit->addControl('Parent Agreement', 'select_radio')
		->name('parentansw')
		->sqlField('parentansw')
		->data(array(
				'Y' => 'The parent agreed to the referral',
				'N' => 'The parent refused the referral'
			)
		)
		->breakRow();

	$edit->addControl('Referral', 'select_check')
		->name('ref_made_prev')
		->sqlField('ref_made_prev')
		->data(array(
				'Y' => 'Referral previously made.',
			)
		)
		->displaySelectAllButton(false);

	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
	$edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
	$edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

	$edit->finishURL = 'javascript:api.window.destroy();';
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '40%';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_form_a')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>