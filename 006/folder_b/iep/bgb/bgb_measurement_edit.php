<?php

	Security::init();

	$RefID = io::geti('RefID');
	$as_refid = io::geti('as_refid');

	$edit = new EditClass('Measurement', $RefID);

	$edit->title = 'Add Measurement Item';

	$edit->setSourceTable('webset.std_bgb_measurement', 'm_refid');

	$edit->addGroup('Commom Information');
	$edit->addControl('Description')
		->sqlField('desc_measure')
		->width('200px')
		->req(true);

	$edit->addControl('Type', 'select')
		->sqlField('type_measure')
		->data(
			array(
				'Measurable' => 'Measurable',
				'Non-Measurable' => 'Non-Measurable'
			)
		);

	$edit->addControl(FFMultiSelect::factory('Indicators'))
		->sql("
			SELECT ind_refid, ind_symbol
			  FROM webset.std_bgb_indicator
			 WHERE as_refid = $as_refid
		")
		->sqlTable(
			'webset.std_bgb_measurement_indicator',
			'm_refid',
			array(
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			)
		)
		->sqlField('ind_refid')
		->req(true);

	$edit->addGroup('Update Information')
		->collapsed(true);

	$edit->addControl('Last User', 'protected')
		->sqlField('lastuser')
		->value(SystemCore::$userUID);

	$edit->addControl('Last Update', 'protected')
		->sqlField('lastupdate')
		->value(date('m-d-Y H:i:s'));

	$edit->addControl('vndrefid', 'hidden')
		->sqlField('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->addControl('as_refid', 'hidden')
		->sqlField('as_refid')
		->value($as_refid);

	$edit->printEdit();
?>