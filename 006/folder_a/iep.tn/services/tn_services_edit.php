<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$RefID = io::geti('RefID');

	$edit = new EditClass('edit', $RefID);

	$edit->setSourceTable('webset.std_tn_ns', 'stn_refid');

	$edit->title = 'Add/Edit Services';

	$edit->addGroup('General Information');

	$edit->addControl('Service', 'select')
		->sql("
			SELECT refid,
			       nsdesc
			  FROM webset.disdef_oh_ns
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(recdeactivationdt, NOW()) >= NOW()
			 ORDER BY nsdesc
        ")
		->name('service')
		->sqlField('serv_refid')
		->req();

	$edit->addControl('Comments or Other Service')
		->sqlField('stn_other')
		->width(700);

	$goalsInf = IDEAStudent::factory($tsRefID)->getBgbGoals();
	$goalsArr = array();

	foreach ($goalsInf as $goal) {
		$goalsArr[] = array(
			$goal['grefid'],
			$goal['bl_num']  . '.' . $goal['g_num'] . ' ' . $goal['gsentance']
		);
	}

	$goals = ListClassContent::factory('Goals')
		->addColumn('Goals', 'gsentance')
		->fillData($goalsArr);

	$SQL = "
		SELECT grefid
		  FROM webset.std_tn_ns_goal
		 WHERE stn_refid = " . $RefID . "
	";
	$outcome = db::execSQL($SQL)->indexCol();
	$edit->addControl(FFMultiSelect::factory('Outcome #/s'))
		->name('grefids')
		->value(implode(',', $outcome))
		->addColumn(250, 1)
		->rows(20)
		->css('width', '900')
		->setSearchList($goals);

	$edit->addControl('Provider')
		->sqlField('stn_provider')
		->width(700);

	$edit->addControl(FFSwitchYN::factory('Required'))
		->value('N')
		->sqlField('stn_required_sw');

	$edit->addControl('Starting Date', 'date')
		->sqlField('stn_begdate');

	$edit->addControl('Expected Duration', 'date')
		->sqlField('stn_enddate');

	$edit->addControl('Environment Method', 'select')
		->sqlField('crtrefid')
		->sql("
			SELECT crtrefid, crtdesc
			  FROM webset.disdef_location
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(enddate, NOW()) >= NOW()
			 ORDER BY crtdesc, seqnum
		")
		->emptyOption(true);

	$edit->addControl('Frequency Method', 'select')
		->sqlField('sfrefid')
		->sql("
			SELECT sfrefid, sfdesc
			  FROM webset.disdef_frequency
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(enddate, NOW()) >= NOW()
			 ORDER BY sfdesc, seqnum
		")
		->emptyOption(true);

	$edit->addControl('Intensity Method', 'select')
		->sqlField('int_refid')
		->sql("
			SELECT refid, validvalue
			  FROM webset.disdef_validvalues
			 WHERE valuename = 'TN_Services_Intensity'
			   AND COALESCE(glb_enddate, NOW()) >= NOW()
			 ORDER BY valuename, sequence_number, validvalue ASC
		")
		->emptyOption(true);

	$edit->addControl('Payor')
		->sqlField('stn_payor')
		->width(700);

	$edit->addControl('Review Date', 'date')
		->sqlField('stn_revdate');

	$edit->addControl(FFIDEAValidValues::factory('TN_BGB_Review'))
		->caption('Review Status')
		->maxRecords(1)
		->sqlField('revs_refid');

	$edit->addUpdateInformation();

	$edit->addControl('stdrefid', 'hidden')
		->sqlField('stdrefid')
		->value($tsRefID);

	$edit->addControl('stdrefid', 'hidden')
		->sqlField('iepyear')
		->value($stdIEPYear);

	$edit->setPostsaveCallback('saveOutcome', './tn_service_save.inc.php', array('stn_refid' => $RefID));
	$edit->printEdit();
?>