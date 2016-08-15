<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_EC_MAIN;

	$previous = db::execSQL("
	    SELECT *
		  FROM webset.std_general std
	      WHERE stdrefid = " . $tsRefID . "
		    AND iepyear = " . $stdIEPYear . "
		    AND area_id = " . $area_id . "
	 	  ORDER BY 1 DESC
	")->assoc();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Outcomes/EC Goals';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addTab('General');

	$edit->addControl('Include in EC Builder', 'select_radio')
		->data(array('0' => 'No', '1' => 'Yes'))
		->value('1')
		->sqlField('int10');

	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
				SELECT max(order_num)
				  FROM webset.std_general std
				 WHERE stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
				   AND area_id = " . $area_id . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Document date', 'date')
		->sqlField('dat01');

	$edit->addControl('Outcome', 'select')
		->sqlField('int01')
		->name('int01')
		->sql("
			SELECT refid,
				   (xpath('/record/title/text()', validvalue::xml))[1]
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_EC_Outcomes'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
		     ORDER BY sequence_number, validvalue
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('a. Parent Input', 'textarea')
		->sqlField('txt01')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Related to strengths and concerns in child\'s functioning in this outcome area');

	$edit->addControl('b. State Approved Anchor Assessment and date completed', 'textarea')
		->sqlField('txt02')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true);

	$edit->addControl('c(1). Age Appropriate Skills (same age child)', 'textarea')
		->sqlField('txt03')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Summarizethe specific skills this child has that are age-appropriate, immediate foundational, and/or foundational skills based on assessments, observations and interviews:');

	$edit->addControl('c(2). Immediate Foundational Skills (younger child)', 'textarea')
		->sqlField('txt04')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Summarizethe specific skills this child has that are age-appropriate, immediate foundational, and/or foundational skills based on assessments, observations and interviews:');

	$edit->addControl('c(3). Foundational Skills (much younger child)', 'textarea')
		->autoHeight(true)
		->sqlField('txt05')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Summarizethe specific skills this child has that are age-appropriate, immediate foundational, and/or foundational skills based on assessments, observations and interviews:');

	$edit->addTab('ECO Rating and Progress');
	$edit->addControl('ECO Entry Rating')
		->sqlField('txt06');

	$edit->addControl('Annual ECO Rating and Date')
		->sqlField('txt07')
		->width(200);

	$edit->addControl('Annual ECO Rating and Date')
		->sqlField('txt08')
		->width(200);

	$edit->addControl('ECO Exit Rating')
		->sqlField('txt09');

	$edit->addControl('Progress at exit? Yes/No')
		->sqlField('txt10');

	$edit->addControl('Check one of the following', 'select_radio')
		->sqlField('int02')
		->name('int02')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_EC_Outcomes_Progress'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
		     ORDER BY sequence_number, validvalue
		")
		->breakRow();

	$edit->addTab('Annual Goals');
	$edit->addIFrame(CoreUtils::getURL('ec_goals.php', array('dskey' => $dskey, 'outcome' => $RefID)))->height('500');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');
	$edit->addControl("DS Key", "hidden")->name('dskey')->value($dskey);

	$edit->finishURL = CoreUtils::getURL('ec_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('ec_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
