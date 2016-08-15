<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);


	$edit = new EditClass('', 0);

	$edit->addGroup('General Information');
	$edit->addControl(
		'Frequency and Duration of Supports Required for School Personnel to Implement this IEP include',
		'textarea'
	)
		->name('info')
		->value(
			IDEAStudentRegistry::readStdKey(
				$ds->safeGet('tsRefID')   ,
				'ct_iep'                        ,
				'general_progran_mod'           ,
				$ds->safeGet('stdIEPYear')
			)
		);

	$edit->addControl('', 'hidden')
		->name('tsRefID')
		->value($ds->safeGet('tsRefID'));

	$edit->addControl('', 'hidden')
		->name('stdIEPYear')
		->value($ds->safeGet('stdIEPYear'));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_srv_progmod')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->setPresaveCallback('saveData', 'accommodations_general.inc.php', array('dskey' => $dskey));

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->printEdit();

?>
