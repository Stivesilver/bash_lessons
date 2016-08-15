<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'District-wide Assessments';

        $list->SQL = "
            SELECT std.sdwarefid,
                   state.dwadesc,
                   sdwanarr,
                   CASE std.sdwapartsw WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END
              FROM webset.std_assess_dis std
                   INNER JOIN webset.disdef_assess state ON std.dwarefid = state.dwarefid
             WHERE stdrefid =  " . $tsRefID . "
             ORDER BY dwaseq, dwadesc
        ";

        $list->addColumn('District-wide Assessments');
        $list->addColumn('Narrative');
        $list->addColumn('Student Participate');

        $list->deleteTableName = "webset.std_assess_dis";
        $list->deleteKeyField = "sdwarefid";

        $list->addURL = CoreUtils::getURL('srv_asses_dis.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_asses_dis.php', array('dskey' => $dskey));

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->addButton(
            IDEAFormat::getPrintButton(array('dskey' => $dskey))
        );

        $list->printList();
    } else {

        $edit = new EditClass("edit1", $RefID);

        $edit->title = 'District-wide Assessments';

        $edit->setSourceTable('webset.std_assess_dis', 'sdwarefid');

        $edit->addGroup('General Information');

        $edit->addControl('District-wide assessment', 'select')
            ->sqlField('dwarefid')
            ->name('dwarefid')
            ->sql("
               SELECT dwarefid, dwadesc
                 FROM webset.disdef_assess
                WHERE vndrefid = VNDREFID
                  AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                ORDER BY dwaseq, dwadesc
            ")
            ->emptyOption(true)
            ->req();

        $edit->addControl('Details', 'textarea')
            ->sqlField('sdwanarr');

	    $edit->addControl(FFSwitchYN::factory('Student Participate'))
            ->emptyOption(true)
            ->sqlField('sdwapartsw');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_asses_dis.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_asses_dis.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?>
