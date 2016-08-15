<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$goal = io::geti('goal', true);
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_EC_OBJECTIVES;
	
	$previous = db::execSQL("
	    SELECT *
		  FROM webset.std_general std					   
	      WHERE stdrefid = " . $tsRefID . "
		    AND iepyear = " . $stdIEPYear . "
		    AND area_id = " . $area_id . "
		    AND int01 = " . $goal . "
	 	  ORDER BY 1 DESC 
	")->assoc();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Objectives/Benchmarks';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
				SELECT max(order_num)
				  FROM webset.std_general std					   
				 WHERE stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
				   AND area_id = " . $area_id . "
				   AND int01 = " . $goal . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Objective/Benchmark', 'textarea')
		->sqlField('txt01')
		->css('width', '95%')
		->css('height', '50px')
		->help('required if student takes the IAA')
		->autoHeight(true);


	$edit->addControl('Expected Progress')
		->sqlField('txt02')		
		->width(400);
	
	$edit->addControl('Target Date', 'date')
		->sqlField('dat01');
	
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');
	$edit->addControl("Gaol ID", "hidden")->value($goal)->sqlField('int01');

	$edit->finishURL = CoreUtils::getURL('ec_objectives.php', array('dskey' => $dskey, 'goal' => $goal));
	$edit->cancelURL = CoreUtils::getURL('ec_objectives.php', array('dskey' => $dskey, 'goal' => $goal));

	$edit->printEdit();
?>
