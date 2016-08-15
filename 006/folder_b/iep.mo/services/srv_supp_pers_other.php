<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    if ($_POST) {
        IDEAStudentRegistry::saveStdKey($tsRefID, 'mo_iep', 'personnel_pm', io::post('personnel_pm'));
        IDEAStudentRegistry::saveStdKey($tsRefID, 'mo_iep', 'personnel_ps', io::post('personnel_ps'));
        header('Location: ' . CoreUtils::getURL('srv_supp_pers_other.php', array('dskey' => $dskey)));
    }


    $edit = new EditClass('edit1', 0);

    $edit->addGroup('General Information');

    $edit->addControl('Program Modifications and Accommodations', 'select_check')
        ->value(IDEAStudentRegistry::readStdKey($tsRefID, 'mo_iep', 'personnel_pm'))
        ->name('personnel_pm')
        ->data(
            array(
                1 => 'N\A',
                2 => 'Documented on alternate Form F'
            )
        )
        ->breakRow();


    $edit->addControl('Supports For School Personnel', 'select_check')
        ->value(IDEAStudentRegistry::readStdKey($tsRefID, 'mo_iep', 'personnel_ps'))
        ->name('personnel_ps')
        ->data(
            array(
                1 => 'N\A',
                2 => 'Documented on alternate Form F'
            )
        )
        ->breakRow();

    $edit->finishURL = CoreUtils::getURL('srv_supp_pers_other.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('srv_supp_pers_other.php', array('dskey' => $dskey));
    $edit->saveURL = CoreUtils::getURL('srv_supp_pers_other.php', array('dskey' => $dskey));

    $edit->firstCellWidth = '30%';

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->saveLocal = false;
    $edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
