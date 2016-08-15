<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Case Managers';

	$list->showSearchFields = true;

	$list->SQL = "
            SELECT t1.cmrefid,
                   umlastname || ', ' || umfirstname,
                   t2.umtitle,
                   t3.vouname
              FROM webset.sys_casemanagermst AS t1
                   INNER JOIN public.sys_usermst AS t2 ON t2.umrefid = t1.umrefid
                   INNER JOIN public.sys_voumst AS t3 ON t3.vourefid = t2.vourefid
             WHERE t2.vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY LOWER(t2.umlastname), LOWER(t2.umfirstname)
        ";

	$list->addSearchField(FFIDEASchool::factory(true)->sqlField('t2.vourefid'));
	$list->addSearchField(FFUserName::factory());

	$list->addColumn('Case Manager');
	$list->addColumn('Title');
	$list->addColumn('School');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->addButton(
		IDEAPopulateWindow::factory()
			->addNewItem()
			->setTitle('Add Case Managers')
			->setSQL("
                SELECT umrefid,
                       umlastname || ', ' || umfirstname,
                       umtitle
                  FROM public.sys_usermst usr
                 WHERE vndrefid = VNDREFID
                   AND COALESCE(um_internal, TRUE)
                   AND NOT EXISTS (
						SELECT 1
						  FROM webset.sys_casemanagermst cm
						 WHERE cm.umrefid = usr.umrefid
                       )
                 ORDER BY 2
			")
			->addSearch("User Last Name", "umlastname")
			->addSearch("User First Name", "umfirstname")
			->addColumn('User')
			->addColumn('Title')
			->setDestinationTable('webset.sys_casemanagermst')
			->setDestinationTableKeyField('cmrefid')
			->setSourceTable('public.sys_usermst')
			->setSourceTableKeyField('umrefid')
			->addPair('vndrefid', SystemCore::$VndRefID, FALSE)
			->addPair('lastuser', SystemCore::$userUID, FALSE)
			->addPair('lastupdate', 'NOW()', TRUE)
			->addPair('umrefid', 'umrefid', TRUE)
			->getPopulateButton()
	);

	$list->deleteTableName = "webset.sys_casemanagermst";
	$list->deleteKeyField = "cmrefid";

	$list->printList();

?>
