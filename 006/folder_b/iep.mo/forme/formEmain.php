<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $SQL = "
        SELECT emrefid,
               yesornot
          FROM webset.std_form_e_mst
         WHERE stdrefid = " . $tsRefID . "
           AND syrefid  = " . $stdIEPYear . "
    ";

    $data = db::execSQL($SQL)->assoc();

    $edit = new EditClass('edit1', $stdIEPYear);

    $edit->title = 'Form E: District-Wide Assessments';

    $edit->setSourceTable('webset.std_form_e_mst', 'syrefid');

    $edit->addGroup('General Information');
    $edit->addControl(FFSwitchYN::factory('The student WILL participate in the following District-Wide Assessment(s)'))
		->sqlField('yesornot')
		->req();

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

    $edit->finishURL = 'javascript:api.window.destroy();';
    $edit->cancelURL = 'javascript:api.window.destroy();';

    $edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '50%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_form_e_mst')
            ->setKeyField('syrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();

    if ($data['yesornot'] == 'Y') {
        $list = new ListClass();

        $list->title = 'District-Wide Assessments';

        $list->SQL = "
            SELECT edrefid,
                   assessment,
                   accomodation
              FROM webset.std_form_e_dtl
             WHERE stdrefid = " . $tsRefID . "
               AND syrefid = " . $stdIEPYear . "
               AND assmode = 'D'
             ORDER BY assessment, accomodation
        ";

        $list->addColumn("District Assessment");
        $list->addColumn("Accommodations");

        $list->addURL = CoreUtils::getURL('formEdass.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
        $list->editURL = CoreUtils::getURL('formEdass.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

        $list->deleteTableName = "webset.std_form_e_dtl";
        $list->deleteKeyField = "edrefid";

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->printList();

        include("notes.php");
    } elseif ($data['yesornot'] == 'N') {
        $list = new ListClass();

        $list->title = 'Alternate Assessments';

        $list->SQL = "
            SELECT edrefid,
                   assessment,
                   accomodation,
                   assesswhynot,
                   assesswhyalt
              FROM webset.std_form_e_dtl
             WHERE stdrefid = " . $tsRefID . "
               AND syrefid = " . $stdIEPYear . "
               AND assmode = 'A'
             ORDER BY assessment, accomodation
        ";

        $list->addColumn("District Assessment");
        $list->addColumn("Alternate Assessment");
        $list->addColumn("Why the child cannot participate in the regular assessment");
        $list->addColumn("Particular alternate assessment selected is appropriate");

        $list->addURL = CoreUtils::getURL('formEaass.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
        $list->editURL = CoreUtils::getURL('formEaass.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

        $list->deleteTableName = "webset.std_form_e_dtl";
        $list->deleteKeyField = "edrefid";

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->printList();

        include("notes.php");
	} else {
        $list = new ListClass();
        
		$list->setMasterRecordID($data['yesornot']);
        $list->title = 'Assessments';

        $list->SQL = "
            SELECT edrefid,
                   assessment,
                   accomodation,
                   assesswhynot,
                   assesswhyalt
              FROM webset.std_form_e_dtl
             WHERE stdrefid = " . $tsRefID . "
               AND syrefid = " . $stdIEPYear . "
             ORDER BY assessment, accomodation
        ";

        $list->addColumn("Assessment");
        $list->addColumn("Alternate Assessment/Accommodations");

        $list->printList();

	}
?>
