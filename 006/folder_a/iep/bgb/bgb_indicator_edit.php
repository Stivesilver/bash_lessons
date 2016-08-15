<?php

	Security::init();

	$RefID = io::geti('RefID');
	$as_refid = io::geti('as_refid');

	$edit = new EditClass('Indicator', $RefID);

	$edit->title = 'Add Indicator Item';

	$edit->setSourceTable('webset.std_bgb_indicator', 'ind_refid');

	$edit->addGroup('Commom Information');
	$edit->addControl('Indicator Symbol')
		->sqlField('ind_symbol')
		->req(true);

	$edit->addControl('Description')
		->sqlField('ind_desc')
		->width('300px')
		->req(true);

	$edit->addControl(FFSwitchYN::factory('Met Mastery'))
		->sqlField('met_mastery')
		->value('Y');

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