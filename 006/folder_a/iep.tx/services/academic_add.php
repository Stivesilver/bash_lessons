<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Course/Curriculum Area';

	$edit->setSourceTable('webset_tx.std_srv_courses', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('Semester')
		->sqlField('semester_txt')
		->size(70)
		->req();


	$helpButon = FFMenuButton::factory('Populate');

	$texts = db::execSQL("
		SELECT COALESCE(validvalueid || ' - ','') || validvalue as validvalue
		  FROM webset.disdef_validvalues
		 WHERE vndrefid = VNDREFID
		   AND valuename = 'TX_Services_Course'
		   AND (glb_enddate IS NULL or now()< glb_enddate)
		 ORDER BY valuename, sequence_number, validvalue ASC
	")->assocAll();

	for ($i = 0; $i < count($texts); $i++) {
		$helpButon->addItem($texts[$i]['validvalue'], '$("#course").val(' . json_encode($texts[$i]['validvalue']) . ')');
	}

	$edit->addControl('Course')
		->sqlField('course')
		->name('course')
		->append(count($texts) > 0 ? $helpButon : '')
		->size(40)
		->req();

	$edit->addGroup('Sp Ed Information');

	$edit->addControl('Sp Ed Frequency', 'select')
		->sqlField('spedfreq')
		->name('spedfreq')
		->sql("
			SELECT frefid,
				   frequency
			  FROM webset_tx.def_srv_frequency
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Frequency')
		->sqlField('spedfreq_oth')
		->name('spedfreq_oth')
		->showIf('spedfreq', db::execSQL("
                                  SELECT frefid
                                    FROM webset_tx.def_srv_frequency
                                   WHERE SUBSTRING(LOWER(frequency), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Sp Ed Duration', 'select')
		->sqlField('spedduration')
		->name('spedduration')
		->sql("
			SELECT drefid,
				   duration
			  FROM webset_tx.def_srv_duration
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Duration')
		->sqlField('spedduration_oth')
		->name('spedduration_oth')
		->showIf('spedduration', db::execSQL("
                                  SELECT drefid
                                    FROM webset_tx.def_srv_duration
                                   WHERE SUBSTRING(LOWER(duration), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Sp Ed Location', 'select')
		->sqlField('spedloc')
		->name('spedloc')
		->sql("
			SELECT lrefid,
				   location
			  FROM webset_tx.def_srv_locations
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Location')
		->sqlField('spedloc_oth')
		->name('spedloc_oth')
		->showIf('spedloc', db::execSQL("
                                  SELECT lrefid
                                    FROM webset_tx.def_srv_locations
                                   WHERE SUBSTRING(LOWER(location), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup('Gen Ed Information');

	$edit->addControl('Gen Ed Frequency', 'select')
		->sqlField('genfreq')
		->name('genfreq')
		->sql("
			SELECT frefid,
				   frequency
			  FROM webset_tx.def_srv_frequency
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Frequency')
		->sqlField('genfreq_oth')
		->name('genfreq_oth')
		->showIf('genfreq', db::execSQL("
                                  SELECT frefid
                                    FROM webset_tx.def_srv_frequency
                                   WHERE SUBSTRING(LOWER(frequency), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Gen Ed Duration', 'select')
		->sqlField('genduration')
		->name('genduration')
		->sql("
			SELECT drefid,
				   duration
			  FROM webset_tx.def_srv_duration
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Duration')
		->sqlField('genduration_oth')
		->name('genduration_oth')
		->showIf('genduration', db::execSQL("
                                  SELECT drefid
                                    FROM webset_tx.def_srv_duration
                                   WHERE SUBSTRING(LOWER(duration), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Gen Ed Location', 'select')
		->sqlField('genloc')
		->name('genloc')
		->sql("
			SELECT lrefid,
				   location
			  FROM webset_tx.def_srv_locations
			 WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum
		")
		->req();

	$edit->addControl('Specify Location')
		->sqlField('genloc_oth')
		->name('genloc_oth')
		->showIf('genloc', db::execSQL("
                                  SELECT lrefid
                                    FROM webset_tx.def_srv_locations
                                   WHERE SUBSTRING(LOWER(location), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup('Order Information');
	$edit->addControl('Order #', 'integer')
		->sqlField('order_num')
		->value((int) db::execSQL("
	                    SELECT count(1)
	                      FROM webset_tx.std_srv_courses
	                     WHERE stdrefid = " . $tsRefID . "
						   AND iep_year = " . $stdIEPYear . "
	                ")->getOne() + 1
		)
		->size(20);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iep_year');

	$edit->finishURL = CoreUtils::getURL('academic.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('academic.php', array('dskey' => $dskey));

	$edit->printEdit();
	
?>
