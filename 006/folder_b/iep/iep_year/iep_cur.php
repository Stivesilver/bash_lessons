<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $std_title = IDEAStudentCaption::get($tsRefID);
	$refresh = io::geti('refresh');

    #Refresh Data Storage
    $student = new IDEAStudent($tsRefID);
    $ds->set('stdIEPYear', $student->get('stdiepyear'));

	$set_ini = IDEAFormat::getIniOptions();
	$iepYearTitle = array_key_exists('iep_year_title', $set_ini) ? $set_ini['iep_year_title'] : 'IEP Year';
	$iepTitle = array_key_exists('iep_title', $set_ini) ? $set_ini['iep_title'] : 'IEP';

    $list = new ListClass();

    $list->title = 'Select ' . $iepYearTitle;

	$list->SQL = "
        SELECT siymrefid,
               siymiepbegdate AS begdate,
               siymiependdate AS enddate,
               COALESCE(siymcurrentiepyearsw, 'N') AS siymcurrentiepyearsw,
               lastuser,
               lastupdate
	   	  FROM webset.std_iep_year
		 WHERE stdrefid = " . $tsRefID . "
         ORDER BY siymiepbegdate DESC
    ";

    $list->addColumn('Anticipated ' . $iepTitle . ' Initiation Date')
	    ->type('date')
	    ->sqlField('begdate')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Anticipated ' . $iepTitle . ' Annual Review Date')
	    ->type('date')
	    ->sqlField('enddate')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Current ' . $iepYearTitle )
	    ->type('switch')
	    ->sqlField('siymcurrentiepyearsw')
	    ->cssCallback('markCurrentYearCSS')
        ->width('1px')
        ->align('center');

    $list->addColumn('Last User')
	    ->sqlField('lastuser')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Last Update')
	    ->type('date')
	    ->sqlField('lastupdate')
	    ->cssCallback('markCurrentYearCSS');

    $list->editURL = CoreUtils::getURL('iep_cur_add.php', array('dskey' => $dskey));
    $list->hideCheckBoxes = true;
    $list->multipleEdit = false;

    $list->printList();

	#------------------------------- functions  -------------------------------#

	function markCurrentYearCSS($data) {
		if ($data['siymcurrentiepyearsw'] == 'Y') {
			return array(
				'color' => 'blue',
				'font-weight' => 'bold'
			);
		}
		return array();
	}

	if($refresh) {
		io::js($ds->safeGet('refresh_screen_js'));
	}
    io::js('api.window.changeTitle(' . json_encode($std_title) . ')');
?>
