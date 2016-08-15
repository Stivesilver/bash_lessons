<?php

	Security::init();

	$RefID = io::geti('RefID');
	$edit  = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset.med_state_provider_attr', 'mspa_refid');

	$edit->title       = 'Add/Edit Data';
	$edit->saveAndAdd  = true;

	$edit->addGroup('General Information');
	$edit->addControl('State', 'select')
		->name('screfid')
		->sqlField('screfid')
		->sql("
			SELECT staterefid,
				   state
			  FROM webset.glb_statemst
			 ORDER BY staterefid
		")
		->value(25);

	$edit->addControl('Medicaid Provider Type', 'select')
		->sql("
			SELECT mdp_refid,
				   mdp_provider_type
			  FROM webset.med_def_provider
			 WHERE mdp_refid NOT IN (SELECT mdp_refid FROM webset.med_state_provider_attr WHERE screfid = VALUE_01)
			 ORDER BY mdp_provider_type
			")
		->sqlField('mdp_refid')
		->tie('screfid');

	$edit->addControl('Prescriptions', 'select_radio')
		->sqlField('mspa_prescriptions')
		->sql(IDEADef::getValidValueSql('Medicaid_Prescription', 'validvalueid, validvalue'))
		->value('Y');

	$edit->addControl('Needs Approval', 'select_radio')
		->sqlField('mspa_approval')
		->sql(IDEADef::getValidValueSql('Medicaid_Approval', 'validvalueid, validvalue'))
		->value('N');

	$edit->addUpdateInformation();

	$edit->printEdit();