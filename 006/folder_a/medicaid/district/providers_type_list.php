<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Medicaid Service Provider Types';

	$list->SQL = "
		SELECT mpt.mpt_refid,
			   mpt.mpt_code,
			   mpt.mpt_type,
			   str.strcode,
			   mpt2.mpt_type AS mpt_type_approver,
			   mpt.mpt_admin_sw,
			   mpt.mpt_status_sw
		  FROM webset.med_disdef_provider_types AS mpt
			   LEFT OUTER JOIN webset.med_disdef_provider_types AS mpt2 ON mpt.mpt_refid_approve = mpt2.mpt_refid
			   LEFT OUTER JOIN webset.statedef_services_rel AS str ON str.strrefid = mpt.strrefid
		 WHERE mpt.vndrefid = VNDREFID
			   ADD_SEARCH
		 ORDER BY mpt.mpt_code
	";

	$list->addSearchField('Code', 'mpt.mpt_code')
		->width('40px');

	$list->addSearchField('Medicaid Provider Type', 'mpt.mpt_type')
		->width('40px');

	$list->addSearchField('IEP Related Service', 'mpt.strrefid', 'list')
		->emptyOption(true)
		->sql("
			SELECT strrefid, strcode
			  FROM webset.statedef_services_rel
			 ORDER BY strcode
		");

	$list->addSearchField('Service Approver', 'mpt.mpt_refid_approve', 'list')
		->emptyOption(true)
		->sql("
			SELECT mpt_refid, mpt_type
			  FROM webset.med_disdef_provider_types
			 WHERE vndrefid = VNDREFID
			 ORDER BY mpt_type
		");

	$list->addSearchField(FFSwitchYN::factory('Administrator Entry'), 'mpt.mpt_admin_sw');

	$list->addSearchField(FFSwitchAI::factory('Status'), 'mpt.mpt_status_sw')
		->value('A');

	$list->addColumn('Code', '')
		->sqlField('mpt_code');

	$list->addColumn('Medicaid Provider Type', '15%')
		->sqlField('mpt_type');

	$list->addColumn('IEP Related Service', '15%')
		->sqlField('strcode');

	$list->addColumn('Service Approver', '15%')
		->sqlField('mpt_type_approver');

	$list->addColumn('Administrator Entry', '15%', 'switch')
		->sqlField('mpt_admin_sw');

	$list->addColumn('Status', '15%', 'switch')
		->sqlField('mpt_status_sw')
		->param(
			LCCPSwitch::factory()
				->addValue('A', 'Active', 'blue')
				->addValue('I', 'Inactive', '#800000')
        );

	$list->addURL = CoreUtils::getURL('./providers_type_edit.php');
	$list->editURL = CoreUtils::getURL('./providers_type_edit.php');

	$list->deleteKeyField = 'mpt_refid';
	$list->deleteTableName = 'webset.med_disdef_provider_types';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();
?>