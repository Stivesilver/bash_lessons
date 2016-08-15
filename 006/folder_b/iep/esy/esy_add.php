<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$iep = io::get('iep');
	$url = './esy_list.php';

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Services to be provided during Extended School Year';

	$edit->setSourceTable('webset.std_esy_service_dtl', 'sesysdrefid');

	$edit->addGroup("General Information");

	$edit->addControl("ESY Service", "select")
		->sqlField('serv_id')
		->name('serv_id')
		->sql("
            SELECT desdrefid,desddesc
              FROM webset.disdef_esy_services
             WHERE vndrefid = VNDREFID
               AND COALESCE(desdactivesw, 'Y')!='N'
             ORDER BY trim(desddesc)
        ");

	$edit->addControl('Specify Other/Comments', 'textarea')
		->name('other')
		->sqlField('other')
		->css('WIDTH', '100%')
		->css('HEIGHT', '50px');

	$edit->addControl("Service Begin Date", "date")
		->sqlField('sesysdservicebegdate')
		->value(db::execSQL("
            SELECT begdate
              FROM webset.disdef_esy_dates
             WHERE vndrefid = VNDREFID
        ")->getOne());

	$edit->addControl("Service End Date", "date")
		->sqlField('sesysdserviceenddate')
		->value(db::execSQL("
            SELECT enddate
              FROM webset.disdef_esy_dates
             WHERE vndrefid = VNDREFID
        ")->getOne());

	$edit->addGroup("Frequency/Duration");
	$edit->addControl("Time", "integer")->sqlField('sesysdservicefreqminutes')->size(5);

	$edit->addControl("Amount", "select")
		->sqlField('sesysdservicefreqrefid')
		->sql("
            SELECT esfdrefid,
                   esfddesc
              FROM webset.statedef_esy_serv_freq_desc
             WHERE screfid = " . VNDState::factory()->id . "
             ORDER BY esfddesc
        ");

	$edit->addControl("Frequency", "select")
		->sqlField('sesysdservicefrequomrefid')
		->name('sesysdservicefrequomrefid')
		->sql("
            SELECT esfumrefid,
                   esfumdesc
              FROM webset.statedef_esy_serv_freq_unit_of_measur
             WHERE screfid = " . VNDState::factory()->id . "
               AND COALESCE(esfumactivesw, 'Y') = 'Y'
             ORDER BY esfumdesc");

	$edit->addControl('Specify')
		->name('sesysdservicefreqother')
		->sqlField('sesysdservicefreqother')
		->showIf('sesysdservicefrequomrefid', db::execSQL("
					SELECT esfumrefid
					  FROM webset.statedef_esy_serv_freq_unit_of_measur
					 WHERE LOWER(esfumdesc) LIKE '%other%'
                ")->indexAll())
		->size(50);

	$edit->addControl("Location", "select")
		->sqlField('sesysdservicelocationrefid')
		->sql("
            SELECT desldrefid,
                   deslddesc
              FROM webset.disdef_esy_serv_loc
             WHERE vndrefid = VNDREFID
               AND COALESCE(desldactivesw, 'Y')!='N'
             ORDER BY deslddesc
        ");

	$edit->addControl(
		FFMultiSelect::factory('Goals')
			->sqlField('goals')
			->sql("
		        SELECT grefid,
		               COALESCE(overridetext,gsentance)
		          FROM webset.std_bgb_goal goal
		               INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
		         WHERE goal.stdrefid = " . $tsRefID . "
		           AND baseline.siymrefid = " . $stdIEPYear . "
		           AND baseline.esy = 'Y'
		         ORDER BY baseline.order_num, baseline.blrefid, goal.order_num, goal.grefid
            ")
	);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	if ($iep == 1) {
		$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
		$url = './esy_list_iep.php';
	}

	$edit->finishURL = CoreUtils::getURL($url, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($url, array('dskey' => $dskey));

	$edit->printEdit();
?>
