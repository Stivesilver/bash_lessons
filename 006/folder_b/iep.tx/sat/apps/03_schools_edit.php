<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);
	$options    = db::execSQL("
		SELECT vouname,
			   vouname
          FROM sys_voumst
         WHERE vndrefid = ". SystemCore::$VndRefID . "
         ORDER BY vouname
        ")->assocAll();

	$data = array();
	foreach ($options as $item) {
		$data[$item['vouname']] = $item['vouname'];
	}

	$edit->setSourceTable('webset_tx.std_sat_schools', 'refid');

	$edit->title     = "Add/Edit Previous Schools Attended";
	$edit->finishURL = $edit->cancelURL = CoreUtils::getURL('03_schools_list.php', array('dskey' => $dskey));

	$edit->addGroup("General Information");
	$edit->addControl("School", "select")
		->sqlField('school')
		->emptyOption(true)
		->data($data);

	$edit->addControl("District/State/Country", "edit")
		->value(SystemCore::$VndName)
		->sqlField('district')
		->size(80);

	$edit->addControl("Dates of enrollment", "edit")->sqlField('dates')->size(50);
	$edit->addControl("Grade", "edit")->sqlField('grades')->size(30);
	$edit->addControl("Sequence Number", "integer")->sqlField('seqnum')->size(10);
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->printEdit();
?>