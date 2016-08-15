<?php

	Security::init();

	$RefID = io::geti('RefID');

	$edit = new EditClass('EDIT', $RefID);

	$edit->title = 'Add/Edit Medicaid Service Provider Types';

	$edit->setSourceTable('webset.med_disdef_provider_types', 'mpt_refid');

	$edit->addGroup('General Information');
	$edit->addControl('Code', 'text')
		->sqlField('mpt_code')
		->req(true);

	$edit->addControl('Medicaid Provider Type', 'text')
		->sqlField('mpt_type')
		->req(true);

	$edit->addControl('IEP Related Service', 'list')
		->sqlField('strrefid')
		->sql("
			SELECT strrefid, strcode
			  FROM webset.statedef_services_rel
			 WHERE screfid = " . VNDState::factory()->id
		)
		->emptyOption(true);

	$edit->addControl('Service Approver', 'list')
		->sqlField('mpt_refid_approve')
		->sql("
			SELECT mpt_refid, mpt_type
			  FROM webset.med_disdef_provider_types
			 WHERE vndrefid = VNDREFID
		")
		->emptyOption(true);

	$edit->addControl(FFSwitchYN::factory('Administrator Entry'))
		->sqlField('mpt_admin_sw')
		->value('Y')
		->req(true);

	$edit->addControl(FFSwitchAI::factory('Status'))
		->sqlField('mpt_status_sw')
		->value('A')
		->req(true);

	$edit->addUpdateInformation();
	$edit->addControl('vndrefid', 'hidden')
		->sqlField('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->setPostsaveCallback('postSave', 'providers_type_edit.inc.php');
	$edit->cancelURL = CoreUtils::getURL('./providers_type_list.php');
	$edit->finishURL = CoreUtils::getURL('./providers_type_list.php');

	$edit->firstCellWidth = '25%';

	$edit->printEdit();

?>