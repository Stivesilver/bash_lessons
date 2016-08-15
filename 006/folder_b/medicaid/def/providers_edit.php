<?php

	Security::init();

	$RefID = io::geti('RefID');
	$edit  = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset.med_def_provider', 'mdp_refid');

	$edit->title       = 'Add/Edit Data';
	$edit->saveAndAdd  = true;
	$edit->saveAndEdit = true;

	$edit->addGroup('General Information');
	$edit->addControl('Provider Type', 'edit')
		->sqlField('mdp_provider_type');

	$edit->addControl('Provider Type Code', 'edit')
		->sqlField('mdp_provider_type_code');

	$edit->addGroup('Status Information');
	$edit->addControl('Record Status', 'select')
		->sqlField('mdp_status')
		->data(
			array(
				'A' => 'Active',
				'R' => 'Retired',
				'P' => 'Pending'
			)
		);

	$edit->addControl('Activation Date', 'date')
		->sqlField('mdp_activation');

	$edit->addControl('Retired Date', 'date')
		->sqlField('mdp_retired');

	$edit->addUpdateInformation();

	$edit->printEdit();