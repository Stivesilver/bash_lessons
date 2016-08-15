<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Instructional Area';

	$edit->setSourceTable('webset_tx.std_academics', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('Instructional Area', 'select')
		->sqlField('ac_refid')
		->name('ac_refid')
		->sql("
			SELECT ac_refid, 
			       ac_desc 
			  FROM webset_tx.def_academics
			 ORDER BY seqnum, ac_desc
		");

	        
    $edit->addControl('Specify Area')
		->sqlField('area_other')
        ->showIf('ac_refid', db::execSQL("
                                  SELECT ac_refid
                                    FROM webset_tx.def_academics
                                   WHERE substring(lower(ac_desc), 1, 5) = 'other'
                                 ")->indexAll())
        ->size(50);
	
	$edit->addControl('Skill Level', 'textarea')
		->sqlField('skill_level')
		->css('width', '100%')
		->css('height', '50px');
	
	$edit->addControl('Strengths', 'textarea')
		->sqlField('strengths')
		->css('width', '100%')
		->css('height', '50px');
	
	$edit->addControl('Needs', 'textarea')
		->sqlField('needs')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('std_refid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iep_year');
	
	$edit->addSQLConstraint('This Area has been already added', 
	    "
        SELECT 1 
	      FROM webset_tx.std_academics
	     WHERE std_refid = " . $tsRefID . "
	       AND iep_year = " . $stdIEPYear . "
		   AND ac_refid = [ac_refid]
	       AND refid != AF_REFID
    ");
	
	$edit->finishURL = CoreUtils::getURL('academics.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('academics.php', array('dskey' => $dskey));

	$edit->printEdit();
?>