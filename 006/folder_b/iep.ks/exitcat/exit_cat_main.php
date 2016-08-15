<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Special Education Status';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup("General Information");

	$edit->addControl("Date Entered Sp Ed Program", "date")->sqlField('stdenterdt')->name('stdenterdt')->req();

	$edit->addControl(FFIDEAEnrollCodes::factory())
		->sqlField('denrefid')
		->name('denrefid')
		->req();

	$edit->addControl("State Status Code: ", "select")
		->sqlField('state_status')
		->sql("
            SELECT statuscode,
                   CAST(statuscode AS VARCHAR)||' - '||shortdesc
              FROM webset.statedef_status_code
             WHERE screfid = " . VNDState::factory()->id . "
             ORDER BY statuscode, shortdesc
        ")
		->emptyOption(true);

	$edit->addControl("Exit Date: ", "date")->sqlField('stdexitdt')->name('stdexitdt');

	$edit->addControl(FFIDEAExitCodes::factory())
		->sqlField('dexrefid')
		->name('dexrefid')
		->emptyOption(true);

	$edit->addControl('Comments', 'textarea')
		->name('id_medical')
		->sqlSavable(FALSE)
		->value(db::execSQL("
			SELECT id_medical
			  FROM webset.std_common
			 WHERE stdrefid = " . $tsRefID . "
		")->getOne())
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->setPresaveCallback('saveComment', 'exit_cat_save.inc.php');

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '30%';

	$edit->addSQLConstraint('New period overlaps previously added Sp Ed Enrollment', "
        SELECT 1
          FROM webset.sys_teacherstudentassignment
         WHERE stdrefid = " . $tsRefID . "
           AND (COALESCE(stdenterdt, '1000-01-01'::date), COALESCE(stdexitdt, '3000-01-01'::date))
                    OVERLAPS
               (CASE WHEN '[stdenterdt]' IN ('', '0') THEN '1000-01-01' ELSE '[stdenterdt]' END::date,
                CASE WHEN '[stdexitdt]'  IN ('', '0') THEN '3000-01-01' ELSE '[stdexitdt]'  END::date)
           AND tsrefid != AF_REFID
    ");

	$edit->addSQLConstraint('End Date should be greater than Start Date', "
        SELECT 1 WHERE '[stdenterdt]' >= '[stdexitdt]' AND '[stdenterdt]' NOT IN ('', '0') AND '[stdexitdt]' NOT IN ('', '0')
    ");

	$edit->addSQLConstraint('Please Specify Exit Code', "
        SELECT 1 WHERE '[dexrefid]' IN ('', '0') AND '[stdexitdt]' NOT IN ('', '0')
    ");

	$edit->addSQLConstraint('Please Specify Exit Date', "
        SELECT 1 WHERE '[dexrefid]' NOT IN ('', '0') AND '[stdexitdt]' IN ('', '0')
    ");

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
