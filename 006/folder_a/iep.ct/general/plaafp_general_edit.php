<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudent($tsRefID);

	#Finds PLAFP table ID
	$SQL = "
        SELECT prefid
          FROM webset.std_plepmst
         WHERE iepyear = " . $stdIEPYear . "
           AND stdrefid = " . $tsRefID . "
    ";

	$result = db::execSQL($SQL);
	if (!$result->EOF) {
		$prefid = $result->fields[0];
	} else {
		$prefid = 0;
	}

	$edit = new EditClass('edit1', $prefid);

	$edit->title = 'Present Levels of Academic Achievement and Functional Performance';

	$edit->setSourceTable('webset.std_plepmst', 'prefid');

	$edit->addGroup('General Information');

	$edit->addControl('Parent and Student Input and Concerns', 'textarea')
		->sqlField('pleadstat')
		->css('width', '100%')
		->css('height', '200px')
		->autoHeight(true);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = 'javascript:parent.switchTab(1);';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->topButtons = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_plepmst')
			->setKeyField('prefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
