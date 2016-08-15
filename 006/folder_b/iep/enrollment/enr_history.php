<?php

    Security::init();
    $stdrefid = io::geti('stdrefid');
    $std_title = db::execSQL("
        SELECT stdfnm || ' ' || stdlnm
          FROM webset.dmg_studentmst
         WHERE stdrefid = " . $stdrefid . "
        ")->getOne() . ', Lumen ID: ' . $stdrefid . ' - Sp Ed Enrollment History';

    if (io::get('RefID') > 0 || io::get('RefID') == '0') {
        $edit = new EditClass('edit1', io::get('RefID'));

        $edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

        $edit->addGroup("General Information");
        $edit->addControl("Date Entered Sp Ed Program", "date")
            ->sqlField('stdenterdt')
            ->name('stdenterdt')
            ->req(IDEACore::disParam(111) == 'Y' ? false : true);

        $edit->addControl(FFIDEAEnrollCodes::factory())
            ->sqlField('denrefid')
            ->name('denrefid')
            ->value(IDEACore::disParam(137))
            ->req();

        $edit->addControl(FFSwitchYN::factory("State Testing"))
            ->sqlField('map_sw')
            ->name('map_sw')
            ->hideIf('mapa_sw', 'Y');

        $edit->addControl(FFSwitchYN::factory("Alternate State Testing"))
            ->sqlField('mapa_sw')
            ->name('mapa_sw')
            ->hideIf('map_sw', 'Y');

        $edit->addControl("Date Exited Sp Ed Program", "date")->sqlField('stdexitdt')->name('stdexitdt');

        $edit->addControl(FFIDEAExitCodes::factory())
            ->sqlField('dexrefid')
            ->name('dexrefid')
            ->emptyOption(true);

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($stdrefid)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));
        $edit->cancelURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));
        $edit->firstCellWidth = "25%";

        $edit->addSQLConstraint('New period overlaps previously added Sp Ed Enrollment', "
            SELECT 1
              FROM webset.sys_teacherstudentassignment
             WHERE stdrefid = " . $stdrefid . "
               AND (COALESCE(stdenterdt, '1000-01-01'::date), COALESCE(stdexitdt, '3000-01-01'::date))
                        OVERLAPS
                   (CASE WHEN '[stdenterdt]' IN ('', '0') THEN '1000-01-01' ELSE '[stdenterdt]' END::date,
                    CASE WHEN '[stdexitdt]'  IN ('', '0') THEN '3000-01-01' ELSE '[stdexitdt]'  END::date)
               AND tsrefid != AF_REFID
        ");

        $edit->addSQLConstraint('End Date should be greater than Start Date', "
            SELECT 1 WHERE '[stdenterdt]' >= '[stdexitdt]' AND '[stdenterdt]' NOT IN ('', '0') AND '[stdexitdt]' NOT IN ('', '0')
        ");

        $edit->addSQLConstraint('Please Specify Exit Code', "
            SELECT 1 WHERE '[dexrefid]' IN ('', '0') AND '[stdexitdt]' NOT IN ('', '0')
        ");

        $edit->addSQLConstraint('Please Specify Exit Date', "
            SELECT 1 WHERE '[dexrefid]' NOT IN ('', '0') AND '[stdexitdt]' IN ('', '0')
        ");

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->multipleEdit = false;

        $list->SQL = "
            SELECT tsrefid,
                   CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END,
                   stdenterdt,
                   COALESCE(dencode || ' - ','') || dendesc,
                   " . IDEAListParts::get('dis_field') . ",
                   map_sw,
                   mapa_sw,
                   to_char(stdexitdt, 'mm-dd-yyyy'),
                   COALESCE(dexcode || ' - ','') || dexdesc
              FROM webset.sys_teacherstudentassignment ts
                   LEFT OUTER JOIN webset.disdef_enroll_codes en ON ts.denrefid = en.denrefid
                   LEFT OUTER JOIN webset.disdef_exit_codes e ON ts.dexrefid = e.dexrefid
             WHERE stdrefid = " . $stdrefid . "
             ORDER BY stdenterdt desc, tsrefid
        ";

        $list->addColumn("Active")->type('switch');
        $list->addColumn("Start Date")->type('date');
        $list->addColumn("Sp Ed Enrollment Code");
        $list->addColumn("Disability");
        $list->addColumn("State Testing");
        $list->addColumn("Alternate State Testing");
        $list->addColumn("Exit Date")->type('date');
        $list->addColumn("Sp Ed Exit Code");

        $list->addRecordsProcess('Delete')
            ->message('Do you really want to delete this Sp Ed Enrollment?')
            ->url(CoreUtils::getURL('enr_history_delete.ajax.php'))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);

        $list->addURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));
        $list->editURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));

        $list->printList();
    }
    io::js('api.window.changeTitle(' . json_encode($std_title) . ');')
?>
