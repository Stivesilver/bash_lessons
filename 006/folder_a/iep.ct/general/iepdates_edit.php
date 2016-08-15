<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'IEP Dates';
	$edit->firstCellWidth = '40%';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');
    $edit->addGroup("General Information");
    $edit->addControl("IEP Meeting Date ", "date")
        ->sqlField('stdiepmeetingdt')
        ->name('stdiepmeetingdt');

    /** @var FFDateTime */
    $stdenrolldt = $edit->addControl('Most Recent Annual Review Date', 'date')
        ->sqlField('stdenrolldt')
        ->name('stdenrolldt');

    /** @var FFDateTime */
    $stdcmpltdt = $edit->addControl('Next Annual Review Date', "date")
        ->sqlField('stdcmpltdt')
        ->name('stdcmpltdt');

    $edit->addControl('Most Recent Eval. Date', 'date')
        ->sqlField('stdevaldt')
        ->name('stdevaldt');

    /** @var FFDateTime */
    $stdtriennialdt = $edit->addControl('Next Reevaluation Date', 'date')
        ->sqlField('stdtriennialdt')
        ->name('stdtriennialdt');

    $edit->addControl('Additional Comments', 'textarea')
        ->sqlField('addcomments')
        ->name('addComments')
        ->css('width', '100%')
        ->css('height', '50px');

    if (IDEACore::disParam(47) != "N") {
		$stdenrolldt->sql("SELECT NULLIF('VALUE_01','')::DATE")
			->tie('stdiepmeetingdt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);

		$stdcmpltdt->sql("SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR'")
			->tie('stdiepmeetingdt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);

		$stdtriennialdt->sql("SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'")
			->tie('stdevaldt')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);
    }

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

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
