<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Special Education Services';
	$edit->firstCellWidth = '30%';

	$edit->setSourceTable('webset.std_srv_sped', 'ssmrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Type', 'select')
		->sqlField('stsrefid')
		->name('stsrefid')
		->sql("
			SELECT stsrefid, COALESCE(stscode || ' - ', '') || stsdesc AS stsdescr
			  FROM webset.statedef_services_all
		     WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
		     ORDER BY stsdescr
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Service Areas', 'select')
		->sqlField('srv_class')
		->name('srv_class')
		->sql("
			SELECT tsnrefid, tsndesc
			  FROM webset.disdef_tsn
		     WHERE vndrefid = VNDREFID
		     ORDER BY tsndesc
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Other Text', 'textarea')
		->showIf('srv_class', db::execSQL("
                                  SELECT tsnrefid
                                    FROM webset.disdef_tsn
                                   WHERE vndrefid = VNDREFID
								     AND lower(tsndesc) = 'other'
                                 ")->indexAll())
		->sqlField('stsother');

	$edit->addControl('Beginning Date', 'date')
		->sqlField('ssmbegdate')
		->value($student->getDate('stdenrolldt'));

	$edit->addControl('Ending Date', 'date')
		->sqlField('ssmenddate')
		->value($student->getDate('stdcmpltdt'));

	$edit->addControl('Time', 'float')
		->sqlField('ssmtime')
		->maxlength(5);

	$edit->addControl('Amount', 'select')
		->sqlField('ssmamt')
		->name('ssmamt')
		->sql("
			SELECT sarefid, sadesc
			  FROM webset.def_spedamt
		     WHERE vndrefid = VNDREFID
			   AND (enddate IS NULL or now()< enddate)
		     ORDER BY seqnum, sadesc
		")
		->value(
			db::execSQL("
				SELECT ssmamt
				  FROM webset.std_srv_sped
				 WHERE stdrefid = " . $tsRefID . "
			   	 ORDER BY ssmrefid DESC
			")->getOne()
		)
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Frequency', 'select')
		->sqlField('ssmfreq')
		->name('ssmfreq')
		->sql("
			SELECT sfrefid, sfdesc
			  FROM webset.def_spedfreq
			 WHERE vndrefid = VNDREFID
			   AND (enddate IS NULL or now()< enddate)
			 ORDER BY seqnum, sfdesc
		")
		->value(
			db::execSQL("
				SELECT ssmfreq
				  FROM webset.std_srv_sped
				 WHERE stdrefid = " . $tsRefID . "
			   	 ORDER BY ssmrefid DESC
			")->getOne()
		)
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Specify')
		->sqlField('freq_oth')
		->name('freq_oth')
		->showIf('ssmfreq', db::execSQL("
			SELECT sfrefid
			  FROM webset.def_spedfreq
		     WHERE vndrefid = VNDREFID
			   AND substring(lower(sfdesc), 1, 5) = 'other'
			")->indexAll()
		)
		->value(
			db::execSQL("
				SELECT freq_oth
				  FROM webset.std_srv_sped
				 WHERE stdrefid = " . $tsRefID . "
			   	 ORDER BY ssmrefid DESC
			")->getOne()
		)
		->size(50);

	$edit->addControl('Location', 'select')
		->sqlField('ssmclasstype')
		->name('ssmclasstype')
		->sql("
			SELECT crtrefid, crtdesc
			  FROM webset.def_classroomtype
			 WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 ORDER BY crtdesc
		")
		->value(
			db::execSQL("
				SELECT ssmclasstype
				  FROM webset.std_srv_sped
				 WHERE stdrefid = " . $tsRefID . "
			   	 ORDER BY ssmrefid DESC
			")->getOne()
		)
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Specify')
		->sqlField('loc_oth')
		->name('loc_oth')
		->showIf('ssmclasstype', db::execSQL("
			SELECT crtrefid
			  FROM webset.def_classroomtype
		     WHERE substring(lower(crtdesc), 1, 5) = 'other'
			")->indexAll()
		)
		->value(
			db::execSQL("
				SELECT loc_oth
				  FROM webset.std_srv_sped
				 WHERE stdrefid = " . $tsRefID . "
			   	 ORDER BY ssmrefid DESC
			")->getOne()
		)
		->size(50);

	$edit->addControl('Weeks', 'select')
		->sqlField('weeks')
		->name('weeks')
		->sql("
			SELECT validvalue,
				   validvalue
			  FROM webset.disdef_validvalues
			 WHERE vndrefid = VNDREFID
			   AND valuename = 'IN_ESY_Weeks'
			   AND (((CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1))
			 ORDER BY valuename, sequence_number, validvalue ASC
		")
		->hide(io::get('ESY') != 'Y')
		->emptyOption(TRUE);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_spedmst.php', array('dskey' => $dskey, 'ESY' => io::get('ESY')));
	$edit->cancelURL = CoreUtils::getURL('srv_spedmst.php', array('dskey' => $dskey, 'ESY' => io::get('ESY')));

	$edit->printEdit();
?>
