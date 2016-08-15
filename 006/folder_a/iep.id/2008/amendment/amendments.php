<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = "Amendment Dates";

        $list->multipleEdit = false;

        $list->SQL = "
            SELECT refid,
                   amendate,
                   comments
              FROM webset.std_amendment
             WHERE stdrefid = " . $tsRefID . "
               AND iepyear = " . $stdIEPYear . "
             ORDER BY amendate
        ";

        $list->addColumn("Amendment Date")->type('date');
        $list->addColumn("Comments");

        $list->deleteTableName = "webset.std_amendment";
        $list->deleteKeyField = "refid";

        $list->addURL = CoreUtils::getURL('amendments.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('amendments.php', array('dskey' => $dskey));

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->printList();
    } else {
        $edit = new EditClass("edit1", $RefID);

        $edit->title = "Add/Edit Amendment Dates";

        $edit->setSourceTable('webset.std_amendment', 'refid');

        $edit->addGroup("General Information");

        $edit->addControl("Date Of Amendment", "date")
            ->sqlField('amendate');

		$edit->addControl("Comments", "textarea")
			->sqlField('comments')
			->css("width", "100%")
			->css("height", "50px")
			->autoHeight(true);

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
        $edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

        $edit->finishURL = CoreUtils::getURL('amendments.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('amendments.php', array('dskey' => $dskey));

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    }
?>
