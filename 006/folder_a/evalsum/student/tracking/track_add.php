<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = 'Add/Edit Evaluation Process Tracking';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->setSourceTable('webset.es_std_evalproc', 'eprefid');

	$edit->finishURL = CoreUtils::getURL('track.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('track.php', array('dskey' => $dskey));

	$edit->addGroup('General Information');

	$edit->addControl('Date Signed Consent Received', 'date')
		->sqlField('date_start');

	$edit->addControl('Evaluation Report Type', 'select')
		->sqlField('ev_type')
		->sql("
			SELECT essrtrefid,
			       essrtdescription
			  FROM webset.es_statedef_reporttype
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND essrtdescription != 'Review of Existing Data'
			   AND essrtdescription != 'Evaluation Plan'
			 ORDER BY seq_ord, essrtdescription desc
	");

	$edit->addControl('Reason for Referral', 'select')
		->sqlField('reason_for_referral')
		->sql("
			SELECT rrefid,
			       rdesc
			  FROM webset.es_disdef_ref_reason
			 WHERE vndrefid = VNDREFID
			   AND (recactivationdt IS NULL or now()< recactivationdt)
			 ORDER BY rseq, rdesc desc
	");

	$edit->addControl('Reason for Evaluation', 'select')
		->sqlField('reason_for_eval')
		->name('reason_for_eval')
		->sql("
			SELECT rrefid,
			       rdesc
			  FROM webset.es_disdef_eval_reason
			 WHERE vndrefid = VNDREFID
			   AND (recactivationdt IS NULL or now()< recactivationdt)
			 ORDER BY rseq, rdesc desc
	");


	$edit->addControl('Specify')
		->sqlField('reason_for_eval_oth')
		->showIf('reason_for_eval', db::execSQL("
                                  SELECT rrefid
                                    FROM webset.es_disdef_eval_reason
                                   WHERE vndrefid = VNDREFID
								     AND substring(lower(rdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->printEdit();

	if ($RefID > 0) {
		$tabs = new UITabs('tabs');
		$tabs->addTab('Evaluation Forms')
			->url(CoreUtils::getURL('form_list.php', array('eprefid' => $RefID, 'dskey' => $dskey)))
			->name('forms');
		
		$tabs->addTab('Evaluation Team')
			->url(CoreUtils::getURL('team_list.php', array('eprefid' => $RefID, 'dskey' => $dskey)))
			->name('forms');
		
		print $tabs->toHTML();
	}
	
	
	

	  	
?>
