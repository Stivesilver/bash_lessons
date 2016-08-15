<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);
	$screenURL = $ds->safeGet('screenURL');
	$set_ini = IDEAFormat::getIniOptions();
	$area_id = 177;

	$mt_date = db::execSQL("
		SELECT stdiepmeetingdt
		  FROM webset.sys_teacherstudentassignment
		 WHERE tsrefid = $tsRefID
	")->getOne();

	$refid = db::execSQL("
		SELECT refid
		  FROM webset.std_general
		 WHERE area_id = $area_id
		   AND stdrefid = $tsRefID
	")->getOne();

	$edit = new EditClass('edit1', (int)$refid);

	$edit->setSourceTable('webset.std_general', 'refid');
	$edit->topButtons = true;

	$edit->title = $set_ini['iep_title'] . ' Dates';
	$edit->firstCellWidth = '40%';

	$edit->addGroup("General Information");
	$edit->addControl($set_ini['iep_title'] . " Meeting Date ", "date")
		->name('stdiepmeetingdt')
		->value($mt_date);

	$edit->addControl(FFIDEAIEPTypes::factory($set_ini['iep_title'] . ' Types'))
		->sqlField('int01');

	$edit->addControl('Designated Service Coordinator', 'text')
		->sqlField('txt02');

	$edit->addControl('Service Coordinator Phone #', 'text')
		->sqlField('txt03');

	$edit->addControl('Six Month Review. Date Due', 'date')
		->sqlField('dat01');

	$edit->addControl('Six Month Review. Date Completed', 'date')
		->sqlField('dat02');

	$edit->addControl('Annual IFSP. Date Due', 'date')
		->sqlField('dat03');

	$edit->addControl('Annual IFSP. Date Completed', 'date')
		->sqlField('dat04');

	$edit->addControl('Additional Review Dates', 'textarea')
		->sqlField('txt04');

	$edit->addGroup('Transition Dates');

	$edit->addControl('Notification of Local Education Agency (LEA) by age two. Date Due', 'date')
		->sqlField('dat05');

	$edit->addControl('Notification of Local Education Agency (LEA) by age two. Date Completed', 'date')
		->sqlField('dat06');

	$edit->addControl('Planning Conference with Parent/s, Lead Agency, LEA and other Service Providers, as appropriate. Date Due', 'date')
		->sqlField('dat07');

	$edit->addControl('Planning Conference with Parent/s, Lead Agency, LEA and other Service Providers, as appropriate. Date Completed', 'date')
		->sqlField('dat08');

	$edit->addControl('(At least 90 days, or up to 6 months prior to child’s third birthday) Transition to LEA, as appropriate. Date Due', 'date')
		->sqlField('dat09');

	$edit->addControl('(At least 90 days, or up to 6 months prior to child’s third birthday) Transition to LEA, as appropriate. Date Completed', 'date')
		->sqlField('dat10');

	$edit->addGroup('Natural Environments/Settings');

	$edit->addControl('The natural environment for', 'textarea')->sqlField('txt05');
	$edit->addControl('Includes the following places/settings', 'textarea')->sqlField('txt06');

	$edit->addControl('area_id', 'hidden')->sqlField('area_id')->value($area_id);
	$edit->addControl('stdrefid', 'hidden')->sqlField('stdrefid')->value($tsRefID);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->setPresaveCallback('update_cover_page', './cover_page_edit.inc.php', array('dskey' => $dskey));

	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

	$edit->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction($area_id)
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
