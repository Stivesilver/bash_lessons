<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $std_title = IDEAStudentCaption::get($tsRefID);

	$set_ini = IDEAFormat::getIniOptions();
	$iepYearTitle = array_key_exists('iep_year_title', $set_ini) ? $set_ini['iep_year_title'] : 'IEP Year';
	$iepTitle = array_key_exists('iep_title', $set_ini) ? $set_ini['iep_title'] : 'IEP';

    #Refresh Data Storage
    $student = new IDEAStudent($tsRefID);
    $ds->set('stdIEPYear', $student->get('stdiepyear'));

    $list = new ListClass();

    $list->title = 'New ' . $iepYearTitle . ' Process';

    $list->SQL = "
        SELECT siymrefid,
               siymiepbegdate,
               siymiependdate,
               COALESCE(siymcurrentiepyearsw, 'N') AS siymcurrentiepyearsw,
               lastuser,
               lastupdate
          FROM webset.std_iep_year
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY siymiepbegdate DESC
    ";

    $list->addColumn('Anticipated ' . $iepTitle . ' Initiation Date')
	    ->sqlField('siymiepbegdate')
	    ->type('date')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Anticipated ' . $iepTitle . ' Annual Review Date')
	    ->sqlField('siymiependdate')
	    ->type('date')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Current ' . $iepYearTitle . '')
	    ->sqlField('siymcurrentiepyearsw')
	    ->type('switch')
	    ->cssCallback('markCurrentYearCSS')
	    ->width('1px')
	    ->align('center');

    $list->addColumn('Last User')
	    ->sqlField('lastuser')
	    ->cssCallback('markCurrentYearCSS');

    $list->addColumn('Last Update')
	    ->sqlField('lastupdate')
	    ->type('date')
	    ->cssCallback('markCurrentYearCSS');

    $list->addURL = CoreUtils::getURL('./iep_proc_add.php', array('dskey' => $dskey));

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_iep_year')
            ->setKeyField('siymrefid')
            ->applyListClassMode()
    );

    $list->editURL = CoreUtils::getURL('./iep_proc_add.php', array('dskey' => $dskey));

    if (SystemCore::$AccessType == "1") {

        $list->addRecordsProcess('Delete')
            ->message('Do you really want to delete this ' . $iepYearTitle . '?')
            ->url(CoreUtils::getURL('./iep_proc_delete.ajax.php', array('dskey' => $dskey)))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);
    }

    $list->getButton(ListClassButton::ADD_NEW)->value('Create New ' . $iepYearTitle . '');

    if ($student->get('stdiepyear') > 0) {
        if (IDEACore::disParam(49) == 'Y') {
            $allowCreate = include(CoreUtils::getPhysicalPath('/apps/idea/sys_maint/sped_menu/includes/ks_builder.php'));
            if (!$allowCreate['condition']) {
                $list->getButton(ListClassButton::ADD_NEW)
                    ->disabled()
                    ->help('Can not create New ' . $iepYearTitle . ' until current ' . $iepTitle . ' is archived.');
            }
        }
    }

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

    io::js('api.window.changeTitle(' . json_encode($std_title) . ')');
?>