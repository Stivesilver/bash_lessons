<?php

	Security::init();

	$RefID = io::geti('RefID');

	$edit = new EditClass('EDIT', $RefID);

	$edit->title = 'Add/Edit Medicaid Services';

	$edit->setSourceTable('webset.med_disdef_services', 'mds_refid');

	$edit->addGroup('General Information');
	$edit->addControl(FFSwitchAI::factory('Status'))
		->sqlField('mds_status_sw')
		->value('A')
		->req(true);

	$edit->addControl('Code', 'text')
		->sqlField('mds_code')
		->width('155px')
		->req(true)
		->maxLength(20);

	$edit->addControl('CPT Code', 'text')
		->sqlField('mds_cpt_code')
		->width('155px')
		->help('Leave this field blank for Non-Medicaid billable services')
		->maxLength(20);

	$edit->addControl('Description', 'text')
		->sqlField('mds_desc')
		->width('450px')
		->req(true)
		->maxLength(250);

	$edit->addControl(FFSwitchYN::factory('Group Size Required'))
		->sqlField('mds_group_size_required_sw')
		->value('N')
		->req(true);

	$edit->addControl(FFSwitchYN::factory('Comments Required'))
		->sqlField('mds_comments_required_sw')
		->value('N')
		->req(true);

	$edit->addControl(FFSwitchYN::factory('Minutes Required'))
		->sqlField('mds_minutes_required_sw')
		->value('N')
		->req(true);

	$edit->addControl(FFSwitchYN::factory('Start Time Required'))
		->sqlField('mds_start_time_required_sw')
		->value('N')
		->req(true);

	$edit->addGroup('Provider Types');

	$provider_types = MedicaidProviderTypes::factory()
		->getProviderTypesByService($RefID);

	$dskey = DataStorage::factory()
		->set('provider_types', $provider_types)
		->getKey();

	$edit->addControl('dskey', 'hidden')
		->value($dskey)
		->name('dskey');

	foreach ($provider_types as $provider_type) {
		$edit->addControl(FFSwitchYN::factory($provider_type['mpt_code']))
			->name('mpt_refid_' . $provider_type['mpt_refid'])
			->value($provider_type['mdpts_status_sw'] == '' ? 'Y' : $provider_type['mdpts_status_sw']);
	}

	$edit->addUpdateInformation();
	$edit->addControl('vndrefid', 'hidden')
		->sqlField('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->setPostsaveCallback('postSave', 'services_edit.inc.php');
	$edit->cancelURL = CoreUtils::getURL('./services_list.php');
	$edit->finishURL = CoreUtils::getURL('./services_list.php');

	$edit->firstCellWidth = '25%';

	$edit->printEdit();
?>