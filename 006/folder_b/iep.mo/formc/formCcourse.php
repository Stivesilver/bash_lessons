<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $RefID = io::get('RefID');
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    if ($RefID == '0' or $RefID > 0) {

        $edit = new EditClass('edit1', (int) $RefID);

        $edit->title = 'Course of Study';

        $edit->setSourceTable('webset.std_form_c_courses', 'refid');

        $edit->addGroup('General Information');
        $edit->addControl('School Year', 'select_radio')
            ->sqlField('year_num')
            ->data(array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6))
            ->value(1);

        $edit->addGroup('Semester One');
        $edit->addControl('Course(s)', 'textarea')->sqlField('one_coursestudy');
        $edit->addControl('Area(s)', 'select_check')
            ->sqlField('one_area_ids')
            ->sql("
	    		 SELECT tarefid, tadesc
                   FROM webset.statedef_transarea area
                  WHERE (enddate IS NULL or now()< enddate)
                  ORDER BY seqnum
	    	");

        $edit->addGroup('Semester Two');
        $edit->addControl('Course(s)', 'textarea')->sqlField('two_coursestudy');
        $edit->addControl('Area(s)', 'select_check')
            ->sqlField('two_area_ids')
            ->sql("
	    		 SELECT tarefid, tadesc
                   FROM webset.statedef_transarea area
                  WHERE (enddate IS NULL or now()< enddate)
                  ORDER BY seqnum
	    	");

        $edit->addGroup('Additional Information');
        $edit->addControl('Notes', 'textarea')->sqlField('notes');
        $edit->addControl("Order #", "integer")
            ->sqlField('seqnum')
            ->value((int) db::execSQL("
	                    SELECT max(seqnum)
	                      FROM webset.std_form_c_courses
	                     WHERE stdrefid = " . $tsRefID . "
	                ")->getOne() + 10
            )
            ->size(5);

        $edit->addGroup("Update Information", true);
        $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
        $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
        $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
        $edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

        $edit->finishURL = CoreUtils::getURL('formCcourse.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
        $edit->cancelURL = CoreUtils::getURL('formCcourse.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

        $edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

        $edit->saveAndAdd = true;

        $edit->printEdit();
    } else {

        $list = new ListClass();

        $list->title = 'Course of Study';

        $list->SQL = "
            SELECT refid,
                   'School Year ' || year_num,
                   replace(one_coursestudy, '\r\n', '<br/>'),
                   plpgsql_recs_to_str('SELECT tadesc as column
	                                      FROM webset.statedef_transarea
	                                     WHERE tarefid in (' || COALESCE(one_area_ids, '0') || ')
                                         ORDER BY seqnum', '<br/>'),
                   replace(two_coursestudy, '\r\n', '<br/>'),
                   plpgsql_recs_to_str('SELECT tadesc as column
	                                      FROM webset.statedef_transarea
	                                     WHERE tarefid in (' || COALESCE(two_area_ids, '0') || ')
                                         ORDER BY seqnum', '<br/>'),
                   notes,
                   seqnum
              FROM webset.std_form_c_courses
             WHERE stdrefid = " . $tsRefID . "
             ORDER BY year_num, seqnum, refid
        ";

        $list->addColumn('School Year', '', 'group');
        $list->addColumn('Semester One Courses');
        $list->addColumn('Semester One Areas');
        $list->addColumn('Semester Two Courses');
        $list->addColumn('Semester Two Areas');
        $list->addColumn('Notes');
        $list->addColumn('Order #');

        $list->addURL = CoreUtils::getURL('formCcourse.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
        $list->editURL = CoreUtils::getURL('formCcourse.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

        $list->deleteTableName = "webset.std_form_c_courses";
        $list->deleteKeyField = "refid";

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
    }
?>
