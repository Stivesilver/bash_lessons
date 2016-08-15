<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');
    //$student    = new IDEAStudent($tsRefID);

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'IEP Dates';

    $edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

    $edit->addGroup("General Information");

    $edit->addControl("IEP Meeting Date ", "date")
        ->sqlField('stdiepmeetingdt')
        ->name('stdiepmeetingdt');

    /** @var FFDateTime */
    $stdenrolldt = $edit->addControl('IEP Initiation Date', 'date')
        ->sqlField('stdenrolldt')
        ->name('stdenrolldt');

    /** @var FFDateTime */
    $stdcmpltdt = $edit->addControl('Duration of IEP', "date")
        ->sqlField('stdcmpltdt')
        ->name('stdcmpltdt');

    $edit->addControl('Evaluation Date', 'date')
        ->sqlField('stdevaldt')
        ->name('stdevaldt');

    /** @var FFDateTime */
    $stdtriennialdt = $edit->addControl('Triennial Date: ', 'date')
        ->sqlField('stdtriennialdt')
        ->name('stdtriennialdt');

    $edit->addControl('Draft IEP copy provided on', 'date')
        ->sqlField('stddraftiepcopydt')
        ->name('stddraftiepcopydt');

    $edit->addControl('IEP copy provided on', 'date')
        ->sqlField('stdiepcopydt')
        ->name('stdiepcopydt');

    $edit->addControl('Date/Current Speech/Language Evaluation', 'date')
        ->value(db::execSQL("
            SELECT edccdeval 
               FROM webset.std_in_eligibility 
            WHERE stdrefid = " . $tsRefID . "
        ")->getOne())
        ->name('edccdeval');

    /** @var FFDateTime */
    $nextevaluation = $edit->addControl('Date/Next Speech/Language Evaluation', 'date')
        ->value(db::execSQL("
            SELECT edncdeval 
               FROM webset.std_in_eligibility 
            WHERE stdrefid = " . $tsRefID . "
        ")->getOne())
        ->name('edncdeval');

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

        $nextevaluation->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'
        ")->tie('edccdeval');
    }

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

    $edit->finishURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey));
    $edit->saveURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

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
