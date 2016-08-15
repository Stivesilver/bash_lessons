<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Discipline Policy and Rules';

	$edit->setSourceTable('webset.std_dpr_answ', 'sdarefid');
	
	if (io::get('RefID') == 0) {
		$sql = "
			SELECT dprqrefid, 
			       dprqtext
			  FROM webset.disdef_disciplinepolicy_rules_quest
			 WHERE vndrefid = VNDREFID
               AND dprqrefid NOT IN (
							SELECT dprqrefid 
							  FROM webset.std_dpr_answ 
							 WHERE stdrefid = " . $tsRefID . "
			   )
	   	     ORDER BY seqnum
		";
	} else {
		$sql = "
			SELECT dprqrefid, 
			       dprqtext
			  FROM webset.disdef_disciplinepolicy_rules_quest
			 WHERE vndrefid = VNDREFID
               AND dprqrefid IN (
					SELECT dprqrefid 
					  FROM webset.std_dpr_answ 
					 WHERE sdarefid = " . io::get('RefID') . "
			   )
	   	     ORDER BY seqnum			
		";
	}
	
	$edit->addGroup('General Information');
	
	
	$edit->addControl('Discipline Policy and Rules', 'select_radio')
		->sqlField('dprqrefid')
		->sql($sql)
		->req();

	$edit->addControl(FFSwitchYN::factory('Answer'))
		->sqlField('sdansw')
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('dpr_quest.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('dpr_quest.php', array('dskey' => $dskey));
	
	$edit->printEdit();
?>