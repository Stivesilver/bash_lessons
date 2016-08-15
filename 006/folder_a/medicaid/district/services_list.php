<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Medicaid Services';

	$list->SQL = "
		SELECT *
		  FROM webset.med_disdef_services
		 WHERE vndrefid = VNDREFID
			   ADD_SEARCH
		 ORDER BY mds_code
	";

	$list->addSearchField('Code', 'mds_code')
		->maxLength(20)
		->width('155px');

	$list->addSearchField('CPT Code', 'mds_cpt_code')
		->maxLength(20)
		->width('155px');

	$list->addSearchField('Description', 'mds_desc')
		->maxLength(250)
		->width('450px');

	$list->addSearchField(FFSwitchAI::factory('Status'), 'mds_status_sw')
		->value('A');

	$list->addColumn('Status', '5%', 'switch')
		->sqlField('mds_status_sw')
		->param(
			LCCPSwitch::factory()
				->addValue('A', 'Active', 'blue')
				->addValue('I', 'Inactive', '#800000')
		);

	$list->addColumn('Code', '8%')
		->sqlField('mds_code');

	$list->addColumn('CPT Code', '8%')
		->sqlField('mds_cpt_code');

	$list->addColumn('Description', '')
		->sqlField('mds_desc');

	$list->addColumn('Group', '5%', 'switch')
		->sqlField('mds_group_size_required_sw');

	$list->addColumn('Comments', '5%', 'switch')
		->sqlField('mds_comments_required_sw');

	$list->addColumn('Minutes', '5%', 'switch')
		->sqlField('mds_minutes_required_sw');

	$list->addColumn('Start Time', '5%', 'switch')
		->sqlField('mds_start_time_required_sw');

	$sql = "
		SELECT mpt_refid, mpt_code
		  FROM webset.med_disdef_provider_types
		 WHERE vndrefid = VNDREFID
	";
	$provider_types = db::execSQL($sql)
		->assocAll();

	$count = 0;
	foreach ($provider_types as $provider_type) {
		if ($count++ < 30) {
			$mpt_refid = $provider_type['mpt_refid'];
			$list->addColumn($provider_type['mpt_code'], '5%', 'switch')
				->dataCallback(create_function('$data', 'return getProviderTypes($data, ' . (int)$mpt_refid . ');'));
		} else {
			break;
		}
	}

	$list->addURL = CoreUtils::getURL('./services_edit.php');
	$list->editURL = CoreUtils::getURL('./services_edit.php');

	$list->deleteKeyField = 'mds_refid';
	$list->deleteTableName = 'webset.med_disdef_services';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

	function getProviderTypes($data, $mpt_refid) {
		$sql = "
			SELECT mdpts_status_sw
			  FROM webset.med_disdef_provider_types_services
			 WHERE mpt_refid = " . $mpt_refid . "
			   AND mds_refid = " . $data['mds_refid'];

		$mdpts_status_sw = db::execSQL($sql)
			->getOne();
		return $mdpts_status_sw;
	}
?>