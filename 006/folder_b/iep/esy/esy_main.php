<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

    if (in_array(VNDState::factory()->code, array('MO', 'OH', 'UT'))) {
        $iepmode = true;
        $SQL = "SELECT sesymrefid
              FROM webset.std_esy_mst
             WHERE iepyear = " . $stdIEPYear . "
               AND stdrefid = " . $tsRefID;
    } else {
        $iepmode = false;
        $SQL = "SELECT sesymrefid
              FROM webset.std_esy_mst
             WHERE stdrefid = " . $tsRefID;
    }

    $RefID = (int) db::execSQL($SQL)->getOne();

    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'IEP Team ESY Meeting Date and Decision';

    $edit->setSourceTable('webset.std_esy_mst', 'sesymrefid');

    $edit->addGroup("General Information");
    $edit->addControl("IEP Team ESY Meeting Date", "date")->sqlField('sesymteammeetingdate');

    $edit->addControl("Is the student eligible for ESY services ", "select_radio")
        ->sqlField('sesymesydecisionsw')
        ->sql("SELECT validvalueid,
                      validvalue
                 FROM webset.glb_validvalues
                WHERE valuename = 'MO_ESY_Elig'
                ORDER BY CASE validvalueid = 'W' WHEN TRUE THEN 'Z' ELSE validvalueid END")
        ->breakRow();


    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    if ($iepmode) $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('iepyear');

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;

    $edit->firstCellWidth = '30%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_esy_mst')
            ->setKeyField('sesymrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
