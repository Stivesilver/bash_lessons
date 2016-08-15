<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
    $screenURL = $ds->safeGet('screenURL');

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'ARD/IEP Dates';

    $edit->SQL = "
		SELECT stdiepmeetingdt,
			   stdenrolldt,
			   stdcmpltdt,
			   stdevaldt,
			   stdtriennialdt,
			   stddraftiepcopydt,
			   stdiepcopydt,
			   longard,
			   briefard,
			   dtx.amendment,
			   inituni,
			   addcomments,
			   ts.lastuser,
			   ts.lastupdate
		  FROM webset.sys_teacherstudentassignment ts
			   LEFT OUTER JOIN webset_tx.std_dates dtx ON tsrefid = dtx.stdrefid AND iepyear = $stdIEPYear
		 WHERE tsRefID = " . $tsRefID . "
	";

    $edit->addGroup("General Information");

    $edit->addControl('ARD/IEP Annual Meeting Date', 'date')
        ->sqlField('stdiepmeetingdt')
        ->name('stdiepmeetingdt');

    /** @var FFDateTime */
    $stdenrolldt = $edit->addControl('ARD/IEP Annual Initiation Date', 'date')
        ->sqlField('stdenrolldt')
        ->name('stdenrolldt');

    /** @var FFDateTime */
    $stdcmpltdt = $edit->addControl('ARD/IEP Projected Date of Annual Review', "date")
        ->sqlField('stdcmpltdt')
        ->name('stdcmpltdt');

    $edit->addControl('Full and Individual Evaluation', 'date')
        ->sqlField('stdevaldt')
        ->name('stdevaldt');

    /** @var FFDateTime */
    $stdtriennialdt = $edit->addControl('Reevaluation', 'date')
        ->sqlField('stdtriennialdt')
        ->name('stdtriennialdt');

    $edit->addControl('Draft ARD/IEP copy provided on', 'date')
        ->sqlField('stddraftiepcopydt')
        ->name('stddraftiepcopydt');

    $edit->addControl('ARD/IEP copy provided on', 'date')
        ->sqlField('stdiepcopydt')
        ->name('stdiepcopydt');

    $edit->addControl('Long Ard Meeting Date', 'date')
        ->sqlField('longard')
        ->name('longard');

    $edit->addControl('Brief ARD Meeting Date', 'date')
        ->sqlField('briefard')
        ->name('briefard');
	
    $edit->addControl('IEP Amendment Meeting Date', 'date')
        ->sqlField('amendment')
        ->name('amendment');
	
    $edit->addControl('Initiation Date (for Long, Brief, or Amendment)', 'date')
        ->sqlField('inituni')
        ->name('inituni');

    $edit->addControl('Additional Comments', 'textarea')
        ->sqlField('addcomments')
        ->name('addComments')
        ->css('width', '100%')
        ->css('height', '50px');

    if (IDEACore::disParam(47) != "N") {
        $stdenrolldt->sql("
            SELECT NULLIF('VALUE_01','')::DATE
        ")->tie('stdiepmeetingdt');

        $stdcmpltdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR'
        ")->tie('stdiepmeetingdt');

        $stdtriennialdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'
        ")->tie('stdevaldt');
    }

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

    $edit->finishURL = 'javascript:parent.switchTab(1);';
	$edit->cancelURL = 'javascript:parent.switchTab();';
    $edit->saveURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey));

    $edit->saveLocal = false;
    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '30%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.sys_teacherstudentassignment')
            ->setKeyField('tsrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
