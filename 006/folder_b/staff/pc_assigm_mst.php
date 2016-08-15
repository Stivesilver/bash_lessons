<?php
    Security::init();
    $RefID = io::get('RefID');
    if ($RefID > 0) {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Process Coordinator Assignments';

        $edit->setSourceTable('webset.sys_proccoordmst', 'pcrefid');

        $edit->addGroup('General Information');
        $edit->addControl('Process Coordinator', 'protected')
            ->value(db::execSQL("
                SELECT umlastname || ', ' || umfirstname
                  FROM webset.sys_proccoordmst pca
                       INNER JOIN sys_usermst usr ON pca.umrefid = usr.umrefid
                 WHERE pcrefid = ".$RefID."
            ")->getOne());

        $edit->cancelURL = CoreUtils::getURL('pc_assigm_mst.php');
        $edit->getButton(EditClassButton::CANCEL)->value('Back');
        $edit->finishURL = '';

        $edit->printEdit();

        $tabs = new UITabs('tabs');
        $tabs->addTab('Assigned Case Managers')->url(CoreUtils::getURL('pc_assigm_cm.php', array('pcrefid'=>$RefID)));
        print $tabs->toHTML();

    } else {
        $list = new ListClass();

        $list->title = 'Process Coordinator Assignments';
        $list->showSearchFields = true;

        $list->SQL = "
            SELECT pca.pcrefid,
                   usr.umlastname || ' ' || usr.umfirstname,
                   t3.vouname,
                   (SELECT count(1)
                      FROM webset.sys_proccoordassignment pchere
                           INNER JOIN webset.sys_casemanagermst cm ON pchere.cmRefID = cm.umrefid
                           INNER JOIN sys_usermst cmhere ON cmhere.umrefid = cm.umrefid
                     WHERE pchere.pcrefid = pca.pcrefid)
               FROM webset.sys_proccoordmst pca
                    INNER JOIN sys_usermst usr ON pca.umrefid = usr.umrefid
                    INNER JOIN sys_voumst AS t3 ON t3.vourefid = usr.vourefid
              WHERE usr.vndrefid = VNDREFID
                    ADD_SEARCH
              ORDER BY LOWER(usr.umlastname || ' ' || usr.umfirstname)
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

        $list->addSearchField('Case Manager', "
            EXISTS (SELECT 1
                      FROM webset.sys_proccoordassignment pchere
                           INNER JOIN webset.sys_casemanagermst cm ON pchere.cmrefid = cm.umrefid
                           INNER JOIN sys_usermst cmhere ON cmhere.umrefid = cm.umrefid
                     WHERE pchere.pcrefid = pca.pcrefid
                       AND lower(umlastname)  like '%' || lower(ADD_VALUE) || '%')
        ");

        $list->addColumn('Process Coordinator');
        $list->addColumn('School');
        $list->addColumn('Assigned Case Managers')
            ->type('tablehint')
            ->param("
               SELECT um.umrefid,
                      umlastname || ', ' || umfirstname
                 FROM webset.sys_casemanagermst cm
                      INNER JOIN sys_usermst um ON cm.umrefid = um.umrefid
                      INNER JOIN webset.sys_proccoordassignment pca ON cm.umrefid = pca.cmrefid
                WHERE um.vndrefid = VNDREFID
                  AND pcrefid = AF_REFID
                ORDER BY lower(umlastname || ', ' || umfirstname)
            ")
            ->dataCallback('markEntries');

        $list->editURL = 'pc_assigm_mst.php';

        $list->printList();
    }

    function markEntries($data, $col) {
        return UILayout::factory()
            ->addHTML($data[$col] . ' Case Managers', '[color:blue; text-decoration:underline;]')
            ->toHTML();
    }
?>
