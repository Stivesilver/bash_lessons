<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Medicaid';
	$list->showSearchFields = true;
	$list->printable = true;

	if (io::get('ksa') != "") {
		$ksa = " AND blksa in (" . io::get('ksa') . ")";
	} else {
		$ksa = '';
	}

	$list->SQL = "
		SELECT stdfnm,
			   stdlnm,
			   " . IDEAParts::get('stddob') . " AS dob,
			   " . IDEAParts::get('stdsex') . " AS gender,
			   stdfedidnmbr as ssn,
			   stdmedicatenum as medicaid,
			   stdstateidnmbr as mosis,
			   countcode,
			   stdhadr1 as address,
			   stdhcity as city,
			   stdhstate as state,
			   stdhzip as zip,
			   (SELECT gdfnm || ' ' || gdlnm FROM webset.dmg_guardianmst grd WHERE grd.stdrefid = std.stdrefid ORDER BY seqnumber, gdtype, gdlnm LIMIT 1 ) as contact,
			   (SELECT gdHPhn FROM webset.dmg_guardianmst grd WHERE grd.stdrefid = std.stdrefid ORDER BY gdtype, stdlnm LIMIT 1 ) as contactphone,
			   '' as consent,
			   '' as prescript,
			   " . IDEAParts::get('stdenrolldt') . " as initdate,
			   " . IDEAParts::get('stdtriennialdt') . " as triennialdate,
			   plpgsql_recs_to_str('SELECT COALESCE(gSentance, overridetext) as column
			                          FROM webset.std_bgb_goal goal
			                               INNER JOIN webset.std_bgb_baseline base ON goal.blRefID = base.blRefID
			                               INNER JOIN webset.std_iep_year AS year ON base.siymrefid = year.siymrefid AND siymcurrentiepyearsw=''Y''
			                         WHERE goal.stdrefid = ' || tsrefid || '
			                         $ksa
			                         ORDER BY base.blrefid, grefid
			                         LIMIT 10', ';  ') as goal,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus,
               " . IDEAParts::get('username') . " AS cmfullname
		  FROM webset.sys_teacherstudentassignment ts
		   	 " . IDEAParts::get('studentJoin') . "
		   	 " . IDEAParts::get('casemanJoin') . "
			   LEFT OUTER JOIN webset.statedef_counties AS county  ON county.refid = stdcounty
		 WHERE std.vndrefid = VNDREFID
		 ADD_SEARCH
	  	 ORDER BY UPPER(stdlnm), UPPER(stdfnm)
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFMultiSelect::factory('Key Skill Area')
			->sql("
			SELECT ksa.gdskrefid,
                   COALESCE(domain.gdSDesc || '-> ','') ||
                   COALESCE(scope.gdSSDesc || '-> ','') ||
                   COALESCE(ksa.gdSkSDesc,'')
              FROM webset.disdef_bgb_goaldomainscopeksa ksa
                   INNER JOIN webset.disdef_bgb_goaldomain domain ON ksa.gdRefID = domain.gdRefID
                   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdSRefID = scope.gdSRefID
             WHERE domain.vndRefID = VNDREFID
               AND (domain.enddate IS NULL OR now()< domain.enddate)
               AND (scope.enddate IS NULL OR now()< scope.enddate)
               AND (ksa.enddate IS NULL OR now()< ksa.enddate)
             ORDER BY 2
		")
			->name('ksa')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('First Name')->sqlField('stdfnm');
	$list->addColumn('Last Name')->sqlField('stdlnm');
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Gender')->sqlField('gender');
	$list->addColumn('Ssn')->sqlField('ssn');
	$list->addColumn('Medicaid')->sqlField('medicaid');
	$list->addColumn('Mosis')->sqlField('mosis');
	$list->addColumn('Countcode')->sqlField('countcode');
	$list->addColumn('Address')->sqlField('address');
	$list->addColumn('City')->sqlField('city');
	$list->addColumn('State')->sqlField('state');
	$list->addColumn('Zip')->sqlField('zip');
	$list->addColumn('Contact')->sqlField('contact');
	$list->addColumn('Contactphone')->sqlField('contactphone');
	$list->addColumn('Consent')->sqlField('consent');
	$list->addColumn('Prescript')->sqlField('prescript');
	$list->addColumn('Initdate')->sqlField('initdate');
	$list->addColumn('Triennialdate')->sqlField('triennialdate');
	$list->addColumn('Goal')->sqlField('goal');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
