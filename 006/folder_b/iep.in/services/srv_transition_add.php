<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$key = SystemRegistry::factory()->readKey('webset', 'district_parameters', 'transition_goal_bank_vndrefid', 1);
	if ($key != -1) {
		$goalVnd = current($key);
	} else {
		se("Transition Goal Bank has not been activated. Contact Lumen Administrator.");
	}

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Transition Services';

	$edit->setSourceTable('webset.std_nts_activities', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('Area', 'select_check')
		->sqlField('scope')
		->name('scope')
		->sql("
			SELECT gdsrefid,
			       gdssdesc
		   	  FROM webset.disdef_bgb_goaldomainscope
			 WHERE vndrefid =  " . $goalVnd . "
			   AND (enddate IS NULL or now()< enddate)
			 ORDER BY gdssdesc
		")
		->req();

	$edit->addControl('The school will', 'select')
		->sqlField('acrefid')
		->name('acrefid')
		->sql("
			SELECT acrefid,
                   transervice
              FROM webset.statedef_nts_activities  am
             WHERE screfid = " . VNDState::factory()->id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY sequence, transervice
		")
		->emptyOption(true)
		->req();

	$edit->addControl('Specify Service', 'textarea')
		->sqlField('otheractivitiy')
		->name('otheractivitiy')
		->showIf('acrefid', db::execSQL("
                                  SELECT acrefid
                                    FROM webset.statedef_nts_activities
                                   WHERE substring(lower(transervice), 1, 5) = 'other'
                                 ")->indexAll());

	$edit->addControl('Agency/Person Responsible', 'select_check')
		->sqlField('provider')
		->name('provider')
		->sql("
			SELECT refid,
                   validvalue
              FROM webset.disdef_validvalues
             WHERE vndrefid = VNDREFID
               AND valuename = 'IN_Trans_Agency'
               AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = 1
             ORDER BY sequence_number, validvalue ASC
		")
		->breakRow();

	$edit->addControl('Other')
		->sqlField('otherprovider')
		->showIf('provider', db::execSQL("
                                  SELECT refid
                                    FROM webset.disdef_validvalues
                                    WHERE vndrefid = VNDREFID
						              AND valuename = 'IN_Trans_Agency'
						              AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = 1
								      AND substring(lower(validvalue), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Completion Date', 'date')
		->sqlField('dateend');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_transition.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_transition.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
