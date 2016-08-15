<?php

    Security::init();

	$stdrefid = io::geti('stdrefid');
	$set_ini = IDEAFormat::getIniOptions();
	
    $std_title = db::execSQL("
        SELECT stdfnm || ' ' || stdlnm
          FROM webset.dmg_studentmst
         WHERE stdrefid = " . $stdrefid . "
        ")->getOne() . ', Lumen ID: ' . $stdrefid . ' ' . $set_ini['iep_title'] . ' -  Enrollment History';

    if (io::get('RefID') > 0 || io::get('RefID') == '0') {
        $edit = new EditClass('edit1', io::get('RefID'));

        $edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

        $edit->addGroup("General Information");
        $edit->addControl("Date Entered " . $set_ini['iep_title'] . " Program", "date")
            ->sqlField('stdenterdt')
            ->name('stdenterdt')
            ->req(IDEACore::disParam(111) == 'Y' ? false : true);

        $edit->addControl(FFIDEAEnrollCodes::factory())
            ->sqlField('denrefid')
            ->name('denrefid')
            ->value(IDEACore::disParam(137))
            ->req();

        $edit->addControl("Date Exited " . $set_ini['iep_title'] . " Program", "date")->sqlField('stdexitdt')->name('stdexitdt');

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

        $edit->addSQLConstraint('New period overlaps previously added ' . $set_ini['iep_title'] . ' Enrollment', "
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
        $list->addColumn($set_ini['iep_title'] . " Enrollment Code");
        $list->addColumn("Exit Date")->type('date');
        $list->addColumn($set_ini['iep_title'] . " Exit Code");

        $list->addRecordsProcess('Delete')
            ->message('Do you really want to delete this ' . $set_ini['iep_title'] . ' Enrollment?')
            ->url(CoreUtils::getURL('enr_history_delete.ajax.php'))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);

        $list->addURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));
        $list->editURL = CoreUtils::getURL('enr_history.php', array('stdrefid' => $stdrefid));

        $list->printList();
    }
?>
<script type="text/javascript">
	if (typeof parent.zWindow == undefined) {
		api.window.changeTitle(<?=json_encode($std_title);?>);
	} else {
		parent.zWindow.changeCaption(<?=json_encode($std_title);?>);
		parent.zWindow.changeSystemBarCaption(<?=json_encode($std_title);?>);
	}
</script>
