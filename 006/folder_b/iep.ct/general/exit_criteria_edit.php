<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdrefid = $ds->safeGet('stdrefid');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Exit Criteria';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup("General Information");

	$edit->addControl("Date Entered Sp Ed Program", "protected")->sqlField('stdenterdt')->name('stdenterdt');

	$edit->addControl(FFIDEAEnrollCodes::factory())
		->sqlField('denrefid')
		->name('denrefid')
		->disabled();

	$edit->addControl("Exit Date: ", "date")->sqlField('stdexitdt')->name('stdexitdt');

	$edit->addControl(FFIDEAExitCodes::factory())
		->sqlField('dexrefid')
		->name('dexrefid')
		->emptyOption(true);

	$edit->addControl('Other')
		->name('other')
		->css('width', '30%')
		->sqlField('parcomments')
		->showIf('dexrefid', db::execSQL("
				SELECT dexrefid
                  FROM webset.disdef_exit_codes district
                       LEFT OUTER JOIN webset.statedef_exitcategories state ON state.secrefid = district.statecode_id
                 WHERE vndrefid = VNDREFID
                   AND (state.recdeactivationdt IS NULL or now()<state.recdeactivationdt)
                   AND (district.enddate IS NULL OR now()<district.enddate)
                   AND LOWER(dexdesc) LIKE '%other%'
                ")->indexAll())
		->tie('dexrefid');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '30%';

	$edit->addSQLConstraint('End Date should be greater than Start Date', "
        SELECT 1 WHERE '[stdenterdt]' >= '[stdexitdt]' AND '[stdenterdt]' NOT IN ('', '0') AND '[stdexitdt]' NOT IN ('', '0')
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

	$list = new ListClass();

	$list->title = 'Other Sp Ed Enrollments';
	$list->multipleEdit = false;

	$list->SQL = "
		SELECT tsrefid,
			   CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END,
			   stdenterdt,
			   COALESCE(dencode || ' - ','') || dendesc,
			   " . IDEAListParts::get('dis_field') . ",
			   map_sw,
			   mapa_sw,
			   to_char(stdexitdt, 'mm-dd-yyyy'),
			   COALESCE(dexcode || ' - ','') || dexdesc
		  FROM webset.sys_teacherstudentassignment ts
			   LEFT OUTER JOIN webset.disdef_enroll_codes en ON ts.denrefid = en.denrefid
			   LEFT OUTER JOIN webset.disdef_exit_codes e ON ts.dexrefid = e.dexrefid
		 WHERE stdrefid = " . $stdrefid . "
		   AND tsrefid != " . $tsRefID . "
		 ORDER BY stdenterdt desc, tsrefid
	";

	$list->addColumn("Active")->type('switch');
	$list->addColumn("Start Date")->type('date');
	$list->addColumn("Sp Ed Enrollment Code");
	$list->addColumn("Disability");
	$list->addColumn("State Testing");
	$list->addColumn("Alternate State Testing");
	$list->addColumn("Exit Date")->type('date');
	$list->addColumn("Sp Ed Exit Code");

	$finalHTML = UILayout::factory()
		->newLine('[height: 50%;]')
		->addObject($edit, 'top')
		->newLine('[height: 50%;]')
		->addObject($list, 'top');

	print $finalHTML->toHTML();

?>
