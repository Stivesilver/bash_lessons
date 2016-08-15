<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$key = SystemRegistry::factory()->readKey('webset', 'district_parameters', 'transition_goal_bank_vndrefid', 1);
	if ($key != -1) {
		$goalVnd = current($key);
	} else {
		se("Transition Goal Bank has not been activated. Contact Lumen Administrator.");
	}

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Post Secondary Goals';
	$edit->firstCellWidth = '40%';

	$edit->setSourceTable('webset.std_in_postgoals', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('Area', 'select')
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
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Preface', 'select')
		->sqlField('preface')
		->sql("
			SELECT gsfRefID, replace(gspText,'The student', '" . db::escape($student->get('stdfirstname')) . "')
			  FROM webset.disdef_bgb_goalsentencepreface
			 WHERE vndRefID = " . $goalVnd . "
			   AND (enddate IS NULL or now()< enddate)
			 ORDER BY gspText
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Action', 'select')
		->sqlField('action')
		->value('action')
		->sql("
			SELECT gdskgarefid, 
			       gdskgaaction
			  FROM webset.disdef_bgb_ksaksgoalactions action			  	   
			       INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON ksa.gdskrefid = action.gdskgrefid
		     WHERE gdsrefid = VALUE_01
			   AND (action.enddate IS NULL or now()< action.enddate)
		     ORDER BY gdsKgaAction
		")
		->emptyOption(TRUE)
		->tie('scope')
		->req();

	$edit->addControl('Content', 'select')
		->sqlField('content')
		->sql("
			SELECT gdskgcrefid, gdskgccontent
			  FROM webset.disdef_bgb_scpksaksgoalcontent content
			  	   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON ksa.gdskrefid = content.gdskgrefid				   
		     WHERE gdsrefid = VALUE_01
			   AND (content.enddate IS NULL or now()< content.enddate)
		     ORDER BY gdskgccontent
		")
		->emptyOption(TRUE)
		->tie('scope')
		->req();

	$edit->addControl('Criteria Basis', IDEACore::disParam(123) == 'N' ? 'hidden' : 'edit')
		->sqlField('crbasis');

	$edit->addControl('Condition', 'select')
		->sqlField('condition')
		->sql("
			SELECT crefid, cdesc
			  FROM webset.disdef_bgb_ksaconditions condition
			 	   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON ksa.gdskrefid = condition.blksa				   
			 WHERE gdsrefid = VALUE_01
			   AND (condition.enddate IS NULL or now()< condition.enddate)
			 ORDER BY cdesc
		")
		->emptyOption(TRUE)
		->tie('scope')
		->req();

	$edit->addControl('Post-Secondary Goals have been reviewed and remain appropriate based on student interview and assessment', 'select')
		->sqlField('itstrue')
		->data(
			array(
				'Y' => 'Yes'
			)
		)
		->emptyOption(TRUE);

	$edit->addControl('Order #', 'integer')
		->sqlField('sequence')
		->value(
			(int) db::execSQL("
					SELECT max(sequence)
					  FROM webset.std_in_postgoals
					 WHERE stdrefid = " . $tsRefID . "
	            ")->getOne() + 1
		)
		->size(20);


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('post_goals.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('post_goals.php', array('dskey' => $dskey));

	$edit->printEdit();
?>