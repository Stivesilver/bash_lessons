<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $screenURL = $ds->safeGet('screenURL');

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'IEP Dates';

    $edit->SQL = "
        SELECT stdiepmeetingdt,
               stdcmpltdt,
               stdevaldt,
               stdtriennialdt,
               stdiepcopydt,
               uni_field3,
               uni_field4,
               wa_graddate,
               wa_transother,
               latest_cmda,
               id_medical,
               addComments,
               uni_field1,
               uni_field2,
               t0.lastuser,
               t0.lastupdate
          FROM webset.sys_teacherstudentassignment t0
               LEFT OUTER JOIN webset.std_common t1 ON t0.tsrefid = t1.stdrefid
         WHERE tsrefid = " . $tsRefID . "
    ";

    $edit->addGroup('General Information');
    $edit->addControl('Current IEP Date', 'date')
        ->sqlField('stdiepmeetingdt')
        ->name('stdiepmeetingdt');

    /** @var FFDateTime */
    $stdcmpltdt = $edit->addControl('Projected Annual Review Date', 'date')
        ->sqlField('stdcmpltdt')
        ->name('stdcmpltdt');

    $edit->addControl('Current Evaluation Date', 'date')
        ->sqlField('stdevaldt')
        ->name('stdevaldt');

    /** @var FFDateTime */
    $stdtriennialdt = $edit->addControl('Projected Triennial Date', 'date')
        ->sqlField('stdtriennialdt')
        ->name('stdtriennialdt');

    $edit->addControl('IEP copy provided on', 'date')
        ->sqlField('stdiepcopydt')
        ->name('stdiepcopydt');

    $edit->addControl('Scheduled IEP Date', 'date')
        ->sqlField('uni_field3')
        ->name('uni_field3');

    $edit->addControl('Scheduled IEP Time', 'time')
        ->sqlField('uni_field4')
        ->name('uni_field4');

    $edit->addControl('Early Childhood Transition Date', 'date')
        ->sqlField('wa_graddate')
        ->name('wa_graddate');

    $edit->addControl('Early Childhood Referring Agency ID')
        ->sqlField('wa_transother')
        ->name('wa_transother')
        ->width('300px');

    $edit->addControl('Current CMDA Date', 'date')
        ->sqlField('latest_cmda')
        ->name('latest_cmda');

	$edit->addControl('Medical Information', 'textarea')
		->sqlField('id_medical')
		->name('id_medical')
		->autoHeight(true);

	$edit->addControl('Additional Comments', 'textarea')
		->sqlField('addcomments')
		->name('addcomments')
		->autoHeight(true);

    $edit->addControl(FFSwitchYN::factory('New Referral in district'))
        ->sqlField('uni_field1')
        ->name('uni_field1');

    $edit->addControl(FFSwitchYN::factory('Transfer Student'))
        ->sqlField('uni_field2')
        ->name('uni_field2');


    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

    if (IDEACore::disParam(47) != "N") {

        $stdcmpltdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR'
        ")->tie('stdiepmeetingdt');

        $stdtriennialdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'
        ")->tie('stdevaldt');
    }

    $edit->finishURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey, 'desktop' => io::get('desktop')));
    $edit->saveURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey, 'desktop' => io::get('desktop')));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));

    $edit->saveLocal = false;
    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '40%';

    $edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);

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
