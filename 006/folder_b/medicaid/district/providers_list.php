<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Medicaid Service Providers';

	$list->SQL = "
		SELECT mp_refid,
			   mp_id,
			   mp.mpt_refid,
			   mpt.mpt_type,
			   mp_fname,
			   mp_lname,
			   mp.umrefid,
			   um.umuid,
			   mp_status_sw
		  FROM webset.med_disdef_providers AS mp
			   LEFT OUTER JOIN webset.med_disdef_provider_types AS mpt ON mpt.mpt_refid = mp.mpt_refid
			   LEFT OUTER JOIN public.sys_usermst AS um ON um.umrefid = mp.umrefid
		 WHERE mp.vndrefid = VNDREFID
			   ADD_SEARCH
		 ORDER BY mp_id
	";

	$list->addSearchField('Provider ID', 'mp_id')
		->width('50px');

	$list->addSearchField('Provider Type', 'mp.mpt_refid', 'list')
		->sql("
			SELECT mpt_refid, mpt_type
			  FROM webset.med_disdef_provider_types
			 WHERE vndrefid = VNDREFID
		");

	$list->addSearchField('First Name', 'mp_fname')
		->maxLength(100)
		->width('200px');

	$list->addSearchField('Last Name', 'mp_lname')
		->maxLength(100)
		->width('200px');

	$list->addSearchField(FFSwitchAI::factory('Status'), 'mp_status_sw')
		->value('A');

	$list->addColumn('Provider ID', '12%')
		->sqlField('mp_id');

	$list->addColumn('Provider Type', '12%')
		->sqlField('mpt_type');

	$list->addColumn('First Name', '')
		->sqlField('mp_fname');

	$list->addColumn('Last Name', '')
		->sqlField('mp_lname');

	$list->addColumn('Lumen User', '')
		->sqlField('umuid');

	$list->addColumn('Status', '12%', 'switch')
		->sqlField('mp_status_sw')
		->param(
			LCCPSwitch::factory()
				->addValue('A', 'Active', 'blue')
				->addValue('I', 'Inactive', '#800000')
		);

	$list->addURL = CoreUtils::getURL('./providers_edit.php');
	$list->editURL = CoreUtils::getURL('./providers_edit.php');

	$list->deleteKeyField = 'mp_refid';
	$list->deleteTableName = 'webset.med_disdef_providers';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();
?>