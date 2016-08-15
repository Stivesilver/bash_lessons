<?php
	Security::init();

	$list = new ListClass('list1');

	$list->addRecordLogging(
		StdServicesLog::factory()
	);

	$list->showSearchFields = true;

	$list->title = 'SPEDEX Medicaid Service Approval';

	$list->SQL = "
		SELECT mss_refid,
		       mss_status,
		       vouname,
		       mp_lname || ', ' || mp_fname,
		       mpt_type,
		       mds_desc,
		       mss_desc,
		       array_to_string(
				   array(
					SELECT stdlnm || ', ' || stdfnm
					  FROM webset.med_std_services_visited AS v
					       INNER JOIN webset.vw_dmg_studentmst AS std ON v.stdrefid = std.stdrefid
					 WHERE v.mss_refid = mss.mss_refid
				   ),
				   '; '
		       ),
		       mss_srv_date,
		       mss_srv_time_start,
		       mss.mss_submission,
		       mss_refid
		  FROM webset.med_std_services AS mss
		       LEFT JOIN sys_voumst AS vou ON mss.vourefid = vou.vourefid
		       LEFT JOIN webset.med_disdef_providers AS mp ON mss.mp_refid = mp.mp_refid
		       LEFT JOIN webset.med_disdef_provider_types AS mpt ON mpt.mpt_refid = mp.mpt_refid
		       LEFT JOIN webset.med_disdef_services AS mds ON mss.mds_refid = mds.mds_refid
		       LEFT JOIN webset.vw_dmg_studentmst AS std ON std.stdrefid = mss.stdrefid
		 WHERE mss.vndrefid = VNDREFID
		 ORDER BY mss_refid DESC
	";

	$list->addSearchField('Service Status', "CASE WHEN 'ADD_VALUE' = 'B' THEN mss_status != 'S' ELSE mss_status = 'ADD_VALUE' END", 'select')
		->data(
			array(
				'B' => 'All but Submitted',
				'A' => 'Approved for Submission',
				'N' => 'Needs Approval',
				'E' => 'Error',
				'S' => 'Submitted'
			)
		)
		->value('B');

	$list->addSearchField('Location', 'mss.vourefid', 'select')
		->sql("
			SELECT vourefid, vouname
			  FROM sys_voumst
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(vouname)
		");

	$list->addSearchField('Service Provider', 'mss.mp_refid', 'select')
		->sql("
			SELECT mp_refid,  mp_lname || ', ' || mp_fname || COALESCE(' (' || mp_id || ')', '')
			  FROM webset.med_disdef_providers
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(mp_lname), LOWER(mp_fname)
		");

	$list->addSearchField('Provider Type', 'mp.mpt_refid', 'select')
		->sql("
			SELECT mpt_refid, mpt_code || ' - ' || mpt_type
              FROM webset.med_disdef_provider_types
			 WHERE vndrefid = VNDREFID
			 ORDER BY LOWER(mpt_code), LOWER(mpt_type)
		");

	$list->addSearchField('Service', 'mss.mds_refid', 'select')
		->sql("
			SELECT mds_refid,  COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '')
			  FROM webset.med_disdef_services
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(mds_code), LOWER(mds_desc)
		");

	$list->addSearchField(FFMultiSelect::factory('Student'))
		->sqlField('mss.stdrefid')
		->addColumn(350, 15)
		->setSearchList(IDEAListParts::createListContent('admin'))
		->maxRecords(1);

	$list->addSearchField('Service Date', 'mss.mss_srv_date', 'date_range');

	$list->addSearchField('Service Time', 'mss.mss_srv_time_start', 'time_range');

	$list->addSearchField('Submission Date', 'mss.mss_submission::date', 'date_range');

	$list->addSearchField('Service ID', 'mss_refid', 'int');

	$list->addColumn('Service Status', '5%')
		->type('switch')
		->param(
			LCCPSwitch::factory()
				->addValue('A','Approved for Submission','green')
				->addValue('N','Needs Approval','red')
				->addValue('E','Error','white')
				->addValue('S','Submitted','yellow')
		);

	$list->addColumn('Location', '10%');
	$list->addColumn('Service Provider', '10%');
	$list->addColumn('Provider Type', '10%');
	$list->addColumn('Service', '10%');
	$list->addColumn('Description', '25%');
	$list->addColumn('Visited Students', '15%');

	$list->addColumn('Service Date', '5%')
		->type('date');
	$list->addColumn('Service Time', '5%')
		->type('time');
	$list->addColumn('Submission Date', '5%')
		->type('date');
	$list->addColumn('Service ID', '5%');


	$list->addURL = $list->editURL = './service_capture_edit.php';

	$list->deleteTableName = 'webset.med_std_services';

	$list->deleteKeyField = 'mss_refid';


	$list->printList();
