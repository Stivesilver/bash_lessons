<?php
    Security::init();
    $RefID = io::get('RefID');
    if ($RefID > 0) {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Case Manager Assignments';

        $edit->setSourceTable('webset.sys_casemanagermst', 'cmrefid');

        $edit->addGroup('General Information');
        $edit->addControl('Case Manager', 'protected')
            ->sqlField('umrefid')
            ->value(db::execSQL("
                SELECT umlastname || ', ' || umfirstname
                  FROM sys_usermst usr
                 WHERE umrefid = ".$RefID."
            ")->getOne());

        $edit->cancelURL = CoreUtils::getURL('cm_assigm_mst.php');
        $edit->getButton(EditClassButton::CANCEL)->value('Back');

        $edit->finishURL = '';

        $edit->printEdit();

        $tabs = new UITabs('tabs');
        $tabs->addTab('Assigned Students')->url(CoreUtils::getURL('cm_assigm_std.php', array('umrefid'=>$RefID)));
        print $tabs->toHTML();

    } else {
        $list = new ListClass();

        $list->title = 'Case Managers Assignments';
	    $list->showSearchFields = true;

        $list->SQL = "
            SELECT t1.umrefid,
                   t2.umlastname || ' ' || t2.umfirstname,
                   t3.vouname,
                   (SELECT count(1)
                      FROM webset.sys_teacherstudentassignment ts
                           INNER JOIN webset.dmg_studentmst AS std ON ts.stdrefid = std.stdrefid
                     WHERE umrefid = t1.umrefid
                       AND std.vndrefid = VNDREFID
                       AND ".IDEAParts::get('stdActive')."
                       AND ".IDEAParts::get('spedActive')."
                     )
              FROM webset.sys_casemanagermst AS t1
                   INNER JOIN public.sys_usermst AS t2 ON t2.umrefid = t1.umrefid
                   INNER JOIN public.sys_voumst AS t3 ON t3.vourefid = t2.vourefid
             WHERE t2.vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY LOWER(t2.umlastname), LOWER(t2.umfirstname)
        ";

        $list->addSearchField("Building", "t3.vourefid", "list")
            ->sql("
                SELECT vourefid,
                       vouname
                  FROM public.sys_voumst
                 WHERE vndrefid = VNDREFID
                 ORDER BY vouname
            ");

		$list->addSearchField(FFUserName::factory());
		$list->addSearchField(
			FFStudentName::factory()
			->sqlField("
			EXISTS (
					SELECT 1
					  FROM webset.sys_teacherstudentassignment s_ts
					       INNER JOIN webset.dmg_studentmst s_dmg ON s_ts.stdrefid = s_dmg.stdrefid
					 WHERE s_ts.umrefid = t1.umrefid
					   AND LOWER(COALESCE(stdlnm, '') || ', ' || COALESCE(stdfnm, '')) LIKE LOWER('%' || ADD_VALUE || '%')
			       )
		")
		);

        $list->addColumn('Case Manager');
        $list->addColumn('School');
        $list->addColumn('Assigned Students')
            ->type('tablehint')
            ->param("
                SELECT ".IDEAParts::get('stdname')." || RTRIM(COALESCE(', #'||stdschid, ''), ',# ')
                  FROM webset.sys_teacherstudentassignment ts
                       INNER JOIN webset.dmg_studentmst AS std ON ts.stdrefid = std.stdrefid
                 WHERE umrefid = AF_REFID
                   AND std.vndrefid = VNDREFID
                   AND ".IDEAParts::get('stdActive')."
                   AND ".IDEAParts::get('spedActive')."
                 ORDER BY 1
            ")
            ->dataCallback('markStudents');

        $list->editURL = 'cm_assigm_mst.php';

        $list->printList();
    }

    function markStudents($data, $col) {
        return UILayout::factory()
            ->addHTML($data[$col] . ' students', '[color:blue; text-decoration:underline;]')
            ->toHTML();
    }
?>
