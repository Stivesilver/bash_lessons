<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form B - Extended School Year';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       COALESCE(desddesc || ': ' || other, desddesc) AS service,
		       CASE
		       WHEN LOWER(esfumdesc) LIKE '%other%' THEN sesysdservicefreqother
		       ELSE esfumdesc
		       END AS frequency,
		       deslddesc AS location,
		       f.sesysdservicebegdate AS begdate,
		       f.sesysdserviceenddate AS enddate,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.std_esy_service_dtl f
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON f.stdrefid = ts.tsrefid
			   " . IDEAParts::get('studentJoin') . "
		       INNER JOIN webset.disdef_esy_services srv ON srv.desdrefid = f.serv_id
		       INNER JOIN webset.statedef_esy_serv_freq_desc freq ON freq.esfdrefid = f.sesysdservicefreqrefid
		       INNER JOIN webset.statedef_esy_serv_freq_unit_of_measur meas ON meas.esfumrefid = f.sesysdservicefrequomrefid
			   INNER JOIN webset.disdef_esy_serv_loc loc ON loc.desldrefid = f.sesysdservicelocationrefid 
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField('ESY Service', 'f.serv_id', 'select')
		->sql("
			SELECT desdrefid,
			       desddesc
			  FROM webset.disdef_esy_services
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(desdactivesw, 'Y') != 'N'
			 ORDER BY trim(desddesc)
		");
	$list->addSearchField('Frequency', 'f.sesysdservicefrequomrefid', 'select')
		->sql("
			SELECT esfumrefid,
			       esfumdesc
			  FROM webset.statedef_esy_serv_freq_unit_of_measur
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND COALESCE(esfumactivesw, 'Y') = 'Y'
			 ORDER BY esfumdesc
		");
	$list->addSearchField('Location', 'f.sesysdservicelocationrefid', 'select')
		->sql("
			SELECT desldrefid,
			       deslddesc
			  FROM webset.disdef_esy_serv_loc
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(desldactivesw, 'Y') != 'N'
			 ORDER BY deslddesc
		");
	$list->addSearchField('Initiation Date', 'f.sesysdservicebegdate', 'daterange');
	$list->addSearchField('Ending Date', 'f.sesysdserviceenddate', 'daterange');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn("ESY Service")->sqlField('service');
	$list->addColumn("Frequency Desc")->sqlField('frequency');
	$list->addColumn("Location")->sqlField('location');
	$list->addColumn("Initiation Date")->type('date')->sqlField('begdate');
	$list->addColumn("Ending Date")->type('date')->sqlField('enddate');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>


