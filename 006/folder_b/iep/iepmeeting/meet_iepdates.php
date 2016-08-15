<?php

    Security::init();

    $dskey = io::get('dskey');

    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

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
    $stdcmpltdt = $edit->addControl('Projected Date of Annual IEP Review', "date")
        ->sqlField('stdcmpltdt')
        ->name('stdcmpltdt');

    $edit->addControl('Current Evaluation Date', 'date')
        ->sqlField('stdevaldt')
        ->name('stdevaldt');

    /** @var FFDateTime */
    $stdtriennialdt = $edit->addControl('Triennial Due Date', 'date')
        ->sqlField('stdtriennialdt')
        ->name('stdtriennialdt');

    $edit->addControl('Draft IEP copy provided on', 'date')
        ->sqlField('stddraftiepcopydt')
        ->name('stddraftiepcopydt');

    $edit->addControl('IEP copy provided on', 'date')
        ->sqlField('stdiepcopydt')
        ->name('stdiepcopydt');

    $edit->addControl('Date of Previous IEP', 'date')
        ->sqlField('previousiepdt')
        ->name('previousiepdt');

    $edit->addControl('Copy of Bill of Rights given to parent on', 'date')
        ->sqlField('parentrightdt')
        ->name('parentrightdt');

    $edit->addControl('Procedural Safeguards given to parent on', 'date')
        ->sqlField('stdprocsafeguarddt')
        ->name('stdprocsafeguarddt');

    $edit->addControl('Additional Comments', 'textarea')
        ->sqlField('addComments')
        ->name('addComments')
        ->css('width', '100%')
        ->css('height', '50px');

	if (IDEACore::disParam(47) != "N") {
		$minusOneDayAuto = IDEACore::disParam(138) == 'Y';

		$stdenrolldt->sql("SELECT NULLIF('VALUE_01','')::DATE")
			->tie('stdiepmeetingdt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);

		$stdcmpltdt->sql("SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR'" . ($minusOneDayAuto ? " - INTERVAL '1 DAY'" : ""))
			->tie('stdiepmeetingdt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);

		$stdtriennialdt->sql("SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'" . ($minusOneDayAuto ? " - INTERVAL '1 DAY'" : ""))
			->tie('stdevaldt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);
    }

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

    $edit->finishURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey, 'desktop' => io::get('desktop')));
    $edit->saveURL = CoreUtils::getURL('meet_iepdates_save.php', array('dskey' => $dskey, 'desktop' => io::get('desktop')));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));

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
