<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Present Levels of Academic Achievement and Functional Performance';

	$edit->setSourceTable('webset.std_in_pglp', 'pglprefid');

	$edit->addGroup('General Information');
	$edit->addControl('Area', 'select')
		->sqlField('tsnrefid')
		->name('tsnrefid')
		->sql("
			SELECT tsnrefid, 
			       tsndesc
		   	  FROM webset.disdef_tsn dis
	         WHERE vndrefid = VNDREFID
	           AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	           ". (io::get('RefID') > 0 ? "" : "
	                AND NOT EXISTS (SELECT 1
	                                  FROM webset.std_in_pglp std
	                                 WHERE stdrefid = " . $tsRefID . "
	                                   AND iepyear = " . $stdIEPYear . "
	                                   AND std.tsnrefid = dis.tsnrefid)") ."
	        ORDER BY tsnnum
		")
		->req();

	$edit->addControl(FFSwitchYN::factory('Age Appropriate'))
		->value('Y')
		->sqlField('age_appropriate_sw');

	$edit->addControl('Briefly describe current performance', 'textarea')
		->sqlField('pglpnarrative')
		->css("width", "100%")
		->css("height", "150px");

	$edit->addControl('Strengths', 'textarea')
		->sqlField('strengths')
		->css("width", "100%")
		->css("height", "150px");

	$edit->addControl('Concerns/Needs', 'textarea')
		->sqlField('concerns')
		->css("width", "100%")
		->css("height", "150px");

	$edit->addControl('Impact of student\'s disability', 'textarea')
		->sqlField('impact')
		->css("width", "100%")
		->css("height", "150px")
		->help('Impact of student\'s disability on involvement and progress in the general education curriculum or appropriate preschool activities');

	$edit->addSQLConstraint('You are trying to add duplicate PLAAFP Area', "
        SELECT 1
	      FROM webset.std_in_pglp
	     WHERE stdrefid = " . $tsRefID . "
	       AND iepyear = " . $stdIEPYear . "
	       AND tsnrefid = [tsnrefid]
	       AND pglprefid != AF_REFID
    ");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('plaafp_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('plaafp_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>