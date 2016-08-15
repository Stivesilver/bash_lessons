<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$esy = io::get('esy');
	$refID = io::geti('RefID');
	$staterefid = VNDState::factory()->id;
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);

	if ($esy == "Y") {
		$SQL = " SELECT TO_CHAR(begdate, 'MM/DD/YYYY'),
                        TO_CHAR(enddate, 'MM/DD/YYYY')
                   FROM webset.disdef_esy_dates
                  WHERE vndrefid = " . $_SESSION["s_VndRefID"];
	} else {
		$SQL = " SELECT to_char(stdenrolldt,'MM/DD/YYYY'),
                        to_char(stdcmpltdt,'MM/DD/YYYY')
                   FROM webset.sys_teacherstudentassignment
                  WHERE webset.sys_teacherstudentassignment.tsrefid = " . $tsRefID;
	}

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);
	if ($result) {
		$bgdate = $result->fields[0];
		$endate = $result->fields[1];
	}

	$edit = new editClass("edit1", $refID);

	$edit->setSourceTable('webset.std_oh_ns', 'refid');

	if ($esy == "Y") {
		$edit->title = "Add/Edit ESY Services";
	} else {
		$edit->title = "Add/Edit Services";
	}

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

		$edit->firstCellWidth = "25%";
	//	$edit->onSaveDone = "saveClear";

	$edit->addGroup("General Information");

	$edit->addControl("Type", "SELECT")
		->name('type')
		->sql("
			   SELECT trefid,
			          typedesc
			     FROM webset.statedef_services_type
			    WHERE screfid = " . $staterefid . "
			    ORDER BY seqnum, typedesc
        ")
		->sqlField('servicetype');

	$edit->addControl("Service", "SELECT")
		->sql("
			SELECT refid,
			       nsdesc
			  FROM webset.disdef_oh_ns
			 WHERE vndrefid = ".SystemCore::$VndRefID."
			   AND servicetype = VALUE_01
			   AND COALESCE(recdeactivationdt, now()) >= now()
			 ORDER BY nsdesc
        ")
		->name('service')
		->sqlField('tnsrefid')
		->req()
		->tie('type');

	$edit->addControl('Comments or Other Service', 'text')
		->sqlField('tnsoth');

	$goalsInf = IDEAStudent::factory($tsRefID)->getBgbGoals($esy);
	$goalsArr = array();
	$i = 0;
	foreach ($goalsInf as $goal) {
		$goalsArr[$i][0] = $goal['grefid'];
		$goalsArr[$i][1] = $goal['bl_num']  . '.' . $goal['g_num'] . ' ' . $goal['gsentance'];
		$i++;
	}

	$goals = ListClassContent::factory('Goals')
		->addColumn('Goals', 'gsentance')
		->fillData($goalsArr);

	$edit->addControl(FFMultiSelect::factory('Goal(s) #'))
		->addColumn(250, 1)
		->rows(20)
		->css('width', '900')
		->setSearchList($goals)
		->sqlField('goals');

	$edit->addControl('Frequency', 'text')
		->sqlField('frequency_text');

	$edit->addControl(FFInputDropList::factory('Responsible Staff'))
		->dropListSQL("
			SELECT refid,
			       validvalue
			  FROM webset.disdef_validvalues
			 WHERE valuename = 'CT_Implementor'
			   AND COALESCE(glb_enddate, now()) >= now()
			 ORDER BY valuename, sequence_number, validvalue ASC
		")
		->sqlField('um_title');

	$edit->addControl(FFUserSearch::factory())
		->caption('Service Implementer')
		->sqlField('umrefid');

	$edit->addControl('Implementor Title')
		->sqlField('inarr');

	$edit->addControl('Start Date', 'date')
		->value($student->getDate('stdenrolldt'))
		->sqlField('begdate');

	$edit->addControl('End Date', 'date')
		->value($student->getDate('stdcmpltdt'))
		->sqlField('enddate');

	$edit->addControl('Site', 'select')
		->sql("
			SELECT crtrefid, crtdesc
			  FROM webset.disdef_location
			 WHERE COALESCE(enddate, now()) >= now()
			   AND vndrefid = " . SystemCore::$VndRefID . "
			 ORDER BY seqnum, CASE substring(lower(crtdesc), 1, 5)  WHEN 'other' THEN 'z' ELSE crtdesc END
		")
		->name('locid')
		->sqlField('locid')
		->req();

	$edit->addControl('Specify Site', 'text')
		->showIf('locid', db::execSQL("
			SELECT crtrefid
			  FROM webset.disdef_location
			 WHERE COALESCE(enddate, now()) >= now()
			   AND vndrefid = " . SystemCore::$VndRefID . "
			   AND substring(lower(crtdesc), 1, 5) = 'other'
		")->indexAll())
		->sqlField('locoth');

	$edit->addControl('Description of Instructional Service Delivery', 'textarea')
		->sqlField('addcomments');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('esy', 'hidden')->value($esy)->sqlField('esy');
	$edit->addControl('iepyear', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl('stdrefid', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->saveAndEdit = true;

	$edit->printEdit();
?>
