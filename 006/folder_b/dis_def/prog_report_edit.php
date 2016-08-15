<?php

	Security::init();

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = 'Add/Edit Building Marking Periods';

	$edit->setSourceTable('webset.sch_marking_period', 'smp_refid');
	$edit->addGroup('General Information');

	$edit->addControl('Period')
		->sqlField('smp_period')
		->req(true)
		->size(40);

	$edit->addControl(FFSwitchYN::factory('ESY'))->value('N')->sqlField('esy');

	$maxSiq = (int)db::execSQL("SELECT MAX(smp_sequens) FROM webset.sch_marking_period")->getOne();
	$maxSiq == null ? $maxSiq = 1 : $maxSiq++;

	$edit->addControl(FFSwitchYN::factory('Active Period'))->value('Y')->sqlField('smp_active');
	$edit->addControl('Sequence')
		->sqlField('smp_sequens')
		->value($maxSiq);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("Vndrefid", "HIDDEN")->value($_SESSION["s_VndRefID"])->sqlField('Vndrefid');
	
	$edit->printEdit();

?>
