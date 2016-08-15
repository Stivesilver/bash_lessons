<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Process Coordinators';

	$list->showSearchFields = true;

	$list->SQL = "
            SELECT pcrefid,
                   umlastname,
                   umfirstname,
                   vouname
              FROM webset.sys_proccoordmst AS t1
                   INNER JOIN public.sys_usermst AS t2 ON t2.umrefid = t1.umrefid
                   INNER JOIN public.sys_voumst AS t3 ON t3.vourefid = t2.vourefid
             WHERE t2.vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY LOWER(umlastname), LOWER(umfirstname)
        ";

	$list->addSearchField('Building', 't3.vourefid', 'list')
		->sql("
                SELECT vourefid,
                       vouname
                  FROM public.sys_voumst
                 WHERE vndrefid = VNDREFID
                 ORDER BY vouname
            ");
	$list->addSearchField(FFUserName::factory());

	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('School');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_proccoordmst')
			->setKeyField('pcrefid')
			->applyListClassMode()
	);

	$list->deleteTableName = 'webset.sys_proccoordmst';
	$list->deleteKeyField = 'pcrefid';

	$list->addButton(
		IDEAPopulateWindow::factory()
			->addNewItem()
			->setTitle('Add Process Coordinators')
			->setSQL("
                SELECT umrefid,
                       umlastname || ', ' || umfirstname,
                       umtitle
                  FROM public.sys_usermst usr
                 WHERE vndrefid = VNDREFID
                   AND COALESCE(um_internal, TRUE)
                   AND NOT EXISTS (
						SELECT 1
						  FROM webset.sys_proccoordmst pc
						 WHERE pc.umrefid = usr.umrefid
                       )
                 ORDER BY 2
			")
			->addSearch("User Last Name", "umlastname")
			->addSearch("User First Name", "umfirstname")
			->addColumn('User')
			->addColumn('Title')
			->setDestinationTable('webset.sys_proccoordmst')
			->setDestinationTableKeyField('pcrefid')
			->setSourceTable('public.sys_usermst')
			->setSourceTableKeyField('umrefid')
			->addPair('vndrefid', SystemCore::$VndRefID, FALSE)
			->addPair('lastuser', SystemCore::$userUID, FALSE)
			->addPair('lastupdate', 'NOW()', TRUE)
			->addPair('umrefid', 'umrefid', TRUE)
			->getPopulateButton()
	);

	$list->printList();
?>
