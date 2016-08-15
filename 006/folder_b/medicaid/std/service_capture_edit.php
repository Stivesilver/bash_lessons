<?php

	Security::init();

	$RefID = io::geti('RefID');

	$SQL = "
		SELECT mss_status
		  FROM webset.med_std_services
		 WHERE mss_refid = " . $RefID;
	$mss_status = db::execSQL($SQL)->getOne();

	$showSaveButton = $mss_status == 'S' ? false : true;

	$status_arr = array();
	$status_arr['A'] = 'Approved for Submission';
	$status_arr['N'] = 'Needs Approval';
	$status_arr['E'] = 'Error';
	if ($mss_status == 'S') {
		$status_arr['S'] = 'Submitted';
	}

	$SQL = "
		SELECT mss_status,
		       vouname,
		       mp_lname || ', ' || mp_fname || COALESCE(' (' || mp_id || ')', '') AS mp_name,
		       COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '') AS mds_desc,
		       mss_desc,
		       stdlnm || ', ' || stdfnm AS std_name
		  FROM webset.med_std_services AS mss
		       LEFT JOIN sys_voumst AS vou ON mss.vourefid = vou.vourefid
		       LEFT JOIN webset.med_disdef_providers AS mp ON mss.mp_refid = mp.mp_refid
		       LEFT JOIN webset.med_disdef_provider_types AS mpt ON mpt.mpt_refid = mp.mpt_refid
		       LEFT JOIN webset.med_disdef_services AS mds ON mss.mds_refid = mds.mds_refid
		       LEFT JOIN webset.vw_dmg_studentmst AS std ON std.stdrefid = mss.stdrefid
		 WHERE mss_refid = " . $RefID;

	list($mss_status, $vouname, $mp_name, $mds_desc, $mss_desc, $std_name) = db::execSQL($SQL)->index();

	if ($mss_status) {
		$mss_status = $status_arr[$mss_status];
	}

	$edit = new EditClass('edit1', $RefID);

	$edit->addRecordLogging(
		StdServicesLog::factory()
	);

	$edit->setSourceTable('webset.med_std_services', 'mss_refid');

	$edit->title = 'Add/Edit SPEDEX Medicaid Service Approval';

	$edit->addControl('Service Status', 'select')
		->name('mss_status')
		->sqlField('mss_status')
		->data($status_arr)
		->req()
		->append(getOrigValue('mss_status', $mss_status));

	$edit->addControl('Service ID')
		->sqlField('mss_refid')
		->transparent()
		->readonly();

	$edit->addControl('Location', 'select')
		->name('vourefid')
		->sqlField('vourefid')
		->sql("
			SELECT vourefid, vouname
			  FROM sys_voumst
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(vouname)
		")
		->value(SystemCore::$VOURefID)
		->req()
		->append(getOrigValue('vourefid', $vouname));

	$edit->addControl('Service Provider', 'select')
		->name('mp_refid')
		->sqlField('mp_refid')
		->sql("
			SELECT mp_refid,  mp_lname || ', ' || mp_fname || COALESCE(' (' || mp_id || ')', '')
			  FROM webset.med_disdef_providers
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(mp_lname), LOWER(mp_fname)
		")
		->req()
		->append(getOrigValue('mp_refid', $mp_name));

	$edit->addControl('Provider Type')
		->sql("
	        SELECT mpt_code || ' - ' || mpt_type
	          FROM webset.med_disdef_provider_types
	               INNER JOIN webset.med_disdef_providers ON webset.med_disdef_providers.mpt_refid = webset.med_disdef_provider_types.mpt_refid
	         WHERE mp_refid = COALESCE(NULLIF('VALUE_01', '')::INTEGER, 0)
		")
		->transparent()
		->readonly()
		->tie('mp_refid');

	$edit->addControl('Service', 'select')
		->name('mds_refid')
		->sqlField('mds_refid')
		->sql("
			SELECT mds_refid,  COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '')
			  FROM webset.med_disdef_services
			  WHERE vndrefid = VNDREFID
			  ORDER BY LOWER(mds_code), LOWER(mds_desc)
		")
		->req()
		->append(getOrigValue('mds_refid', $mds_desc));

	$edit->addControl('Description')
		->name('mss_desc')
		->sqlField('mss_desc')
		->size(50)
		->maxlength(250)
		->append(getOrigValue('mss_desc', $mss_desc))
		->req();

	$acc = IDEAListParts::createListContent(
		'admin',
		'std.stdrefid',
		false,
		false,
		"  AND EXISTS (SELECT 1
                         FROM webset.std_srv_rel rel
                              INNER JOIN webset.med_disdef_providers AS pr ON pr.umrefid = rel.umrefid
                         WHERE pr.mp_refid = VALUE_01
                           AND rel.stdrefid = tsrefid
                       )"
	);

	$edit->addControl(FFMultiSelect::factory('Visited Students'))
		->name('stdrefid')
		->sqlField('stdrefid')
		->addColumn(350, 15)
		->setSearchList($acc, 900, 600)
		->sqlTable('webset.med_std_services_visited', 'mss_refid', array('lastuser' => SystemCore::$userUID, 'lastupdate' => date('Y-m-d H:i:s')))
		->tie('mp_refid')
		->append(getOrigValue('stdrefid'));

	$edit->addControl(FFMultiSelect::factory('Not Visited Students'))
		->name('stdrefid_not_visited')
		->sqlField('stdrefid')
		->addColumn(350, 15)
		->setSearchList($acc, 900, 600)
		->sqlTable('webset.med_std_services_not_visited', 'mss_refid', array('lastuser' => SystemCore::$userUID, 'lastupdate' => date('Y-m-d H:i:s')))
		->tie('mp_refid')
		->append(getOrigValue('stdrefid_not_visited'));

/*
	$edit->addControl(FFIDEAStudent::factory())
		->sqlField('stdrefid')
		->name('stdrefid')
		->caption('Student')
		->append(getOrigValue('stdrefid', $std_name))
		->req();
*/
	$edit->addControl('Service Date', 'date')
		->name('mss_srv_date')
		->sqlField('mss_srv_date')
		->append(getOrigValue('mss_srv_date'))
		->req();

	$edit->addControl('Start Service Time', 'time')
		->name('mss_srv_time_start')
		->sqlField('mss_srv_time_start')
		->append(getOrigValue('mss_srv_time_start'))
		->req();

	$edit->addControl('End Service Time', 'time')
		->name('mss_srv_time_end')
		->sqlField('mss_srv_time_end')
		->append(getOrigValue('mss_srv_time_end'))
		->req();

	$fld = $edit->addControl('Submission Date', 'protected')
		->sqlField('mss_submission')
		->name('mss_submission');
	if ($RefID == 0)
		$fld->value(date('Y-m-d H:i:s'));


	$edit->addUpdateInformation();

	$edit->addEnterpriseInformation();

	if (!$showSaveButton) {
		$edit->getButton(EditClassButton::SAVE_AND_FINISH)
			->disabled(true);
		$edit->getButton(EditClassButton::SAVE_AND_ADD)
			->disabled(true);
	}
	$edit->finishURL = './service_capture_list.php';
	$edit->cancelURL = './service_capture_list.php';

	if ($RefID != 0)
		$edit->addButton('The Update Journal', 'callTransactionJournal();');

	$edit->addSQLConstraint('Unable to update the current record because it will overlap another Medicaid Service record for the current Service Provider.', "
		SELECT 1
		  FROM webset.med_std_services
		 WHERE mp_refid = [mp_refid]
		   AND mss_srv_date = '[mss_srv_date]'::DATE
  		   AND '[mss_srv_time_start]'::TIME  < mss_srv_time_end
		   AND '[mss_srv_time_end]'::TIME > mss_srv_time_start
		   AND mss_refid != $RefID
	");

	$edit->printEdit();

	function getOrigValue($fieldName, $current_value = '') {
		global $RefID;
		$SQL = "
			SELECT msl_text_value_new
			  FROM webset.med_std_services_log
			 WHERE mss_refid = $RefID
			   AND msl_name = '$fieldName'
			   AND msl_text_value_new != ''
			 ORDER BY lastupdate
			 LIMIT 1
		";
		$val = db::execSQL($SQL)
			->getOne();
		if ($val && $val != $current_value)
			$val = '&nbsp; <b>Original value:</b> '. $val;
		else
			$val = '';
		return $val;

	}

	$dsKey = DataStorage::factory()->set('mss_refid', $RefID)->getKey();

	io::jsVar('dsKey', $dsKey);


?>
<script>
	function callTransactionJournal() {
		api.window.open(
			'The Update Journal of Medicaid Service Record',
			api.url('service_capture_transaction_log.php', {'dsKey' : dsKey})
		)
			.resize(1000, 600);
	}
</script>