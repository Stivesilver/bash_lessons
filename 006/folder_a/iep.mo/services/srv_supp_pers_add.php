<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	/**
	 *  Initialises Not Applicable Services ID array
	 * @var array
	 */
	$id_na = db::execSQL("
        SELECT ssprefid
          FROM webset.statedef_services_supppersonnel
         WHERE screfid = " . VNDState::factory()->id . "
           AND (enddate IS NULL or now()< enddate)
           AND nasw = 'Y'
    ")->indexAll();

	$edit = new EditClass("edit1", io::geti('RefID'));

	$edit->title = 'Add/Edit Support For School Personnel';

	$edit->setSourceTable('webset.std_srv_supppersonnel', 'sspmrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Support for School Personnel', 'select')
		->sqlField('ssprefid')
		->name('ssprefid')
		->sql("
            SELECT NULL, NULL
             UNION ALL
           (SELECT ssprefid, sspdesc
              FROM webset.statedef_services_supppersonnel
             WHERE screfid = " . VNDState::factory()->id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY seqnum, sspdesc)
        ")
		->req();
	
	$edit->addControl('Specify', 'textarea')
		->sqlField('sspnarrative')
		->sql("
			SELECT validvalue
			  FROM webset.disdef_validvalues
			 WHERE vndrefid = VNDREFID			   
			   AND valuename = 'MO_Personnel_Defaults'
			   AND validvalueid = NULLIF('VALUE_01','')
        ")		
		->css('width', '100%')
		->css('height', '100px')
		->tie('ssprefid')
		->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);

	$edit->addControl('Beginning Date', 'date')
		->sqlField('sspbegdate')
		->value($student->getDate('stdenrolldt'))
		->hideIf('ssprefid', $id_na);

	$edit->addControl('Ending Date', 'date')
		->sqlField('sspenddate')
		->value($student->getDate('stdcmpltdt'))
		->hideIf('ssprefid', $id_na);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_supp_pers_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_supp_pers_list.php', array('dskey' => $dskey));

	$edit->firstCellWidth = '30%';

	$edit->printEdit();
?>
