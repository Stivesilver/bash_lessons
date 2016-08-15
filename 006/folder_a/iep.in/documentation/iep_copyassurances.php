<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'IEP Copy Assurances';

    $edit->setSourceTable('webset.std_in_iepcopyassurances', 'stdrefid');

    $edit->addGroup('General Information');

    $edit->addControl('IEP copy delivery date', 'date')
        ->sqlField('sicadate');

    $edit->addControl(FFUserName::factory())
        ->sqlField('pname')
        ->caption('IEP copy delivery person')
        ->css('width', '400px');

    $edit->addControl('The parent has received a copy of the Procedural Safeguards and has no questions.', 'select')
        ->sqlField('sicasw')
        ->data(array('Y' => 'Yes', 'N' => 'No'))
        ->emptyOption(true);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '50%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_in_iepcopyassurances')
            ->setKeyField('sicarefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
