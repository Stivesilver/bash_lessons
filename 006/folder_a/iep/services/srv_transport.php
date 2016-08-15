<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');
    $student = new IDEAStudent($tsRefID);

    $SQL = "SELECT strefid 
              FROM webset.std_transportation 
             WHERE stdrefid = " . $tsRefID;

    $result = db::execSQL($SQL);
    if (!$result->EOF) {
        $strefid = $result->fields[0];
    } else {
        $strefid = 0;
    }

    $edit = new EditClass("edit1", $strefid);

    $edit->title = 'Add/Edit Transportation';

    $edit->setSourceTable('webset.std_transportation', 'strefid');

    $edit->addGroup('General Information');

    $edit->addControl('Transportation as a Related Service', 'select_radio')
        ->sqlField('stresidenttrans')
        ->name('stresidenttrans')
        ->data(
            array(
                'N' => 'The student does not require transportation as a related service',
                'Y' => 'The student requires transportation as a necessary related service'
            )
        )
        ->breakRow()
        ->req();

    $edit->addControl(FFSwitchYN::factory('The student needs accommodations or modifications for transportation'))
        ->sqlField('stneedtrans')
        ->name('stneedtrans');
    $edit->addControl(FFSwitchYN::factory('Seat Belt'))
        ->sqlField('staide')
        ->showIf('stneedtrans', 'Y');
    $edit->addControl(FFSwitchYN::factory('Harness seat belt (back latch)'))
        ->sqlField('stharness')
        ->showIf('stneedtrans', 'Y');
    $edit->addControl(FFSwitchYN::factory('Wheelchair Lift'))
        ->sqlField('stwheellift')
        ->showIf('stneedtrans', 'Y');
    $edit->addControl(FFSwitchYN::factory('Infant seat'))
        ->sqlField('sttwa')
        ->showIf('stneedtrans', 'Y');
    $edit->addControl(FFSwitchYN::factory('Booster Seat'))
        ->sqlField('staircond')
        ->showIf('stneedtrans', 'Y');
    $edit->addControl(FFSwitchYN::factory('Curb to curb pick up and drop off'))
        ->sqlField('stdoor')
        ->showIf('stneedtrans', 'Y');

    if (IDEACore::disParam(33) == 'Y') {
        $edit->addControl(FFSwitchYN::factory('Door to door'))
            ->sqlField('doortodoor')
            ->showIf('stneedtrans', 'Y');
        $edit->addControl(FFSwitchYN::factory('Taxi'))
            ->sqlField('taxi')
            ->showIf('stneedtrans', 'Y');
        $edit->addControl(FFSwitchYN::factory('Bus with monitor'))
            ->sqlField('buswithmonitor')
            ->showIf('stneedtrans', 'Y');
    }

    $edit->addControl(FFSwitchYN::factory('Other'))
        ->sqlField('stother')
        ->name('stother')
        ->showIf('stneedtrans', 'Y');

    $edit->addControl('Specify')
        ->sqlField('stothertxt')
        ->showIf('stother', 'Y')
        ->size(50);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->firstCellWidth = "50%";
    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_transportation')
            ->setKeyField('strefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?> 
