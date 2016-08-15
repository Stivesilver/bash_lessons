<?php
	Security::init();

	$list = new listClass();

	$list->multipleEdit = "no";

	$list->customSearch = "yes";
	$list->showSearchFields = "yes";


	$list->SQL = "
		SELECT crtrefid,
               crtdesc,
               CASE crtnarrsw WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END AS crtnarrsw,
               CASE WHEN NOW() >= COALESCE(recactivationdt, TO_DATE('01-01-1000', 'dd-mm-yyyy')) AND
                         recdeactivationdt IS NOT NULL AND
                         NOW() <  recdeactivationdt THEN 'Active'
                    WHEN NOW() >= COALESCE(recactivationdt, TO_DATE('01-01-1000', 'dd-mm-yyyy')) AND
                         recdeactivationdt IS NULL THEN 'Active Indefinitely'
                    ELSE 'Inactive: '||(SELECT srsddesc FROM webset.def_systemrecordstatus WHERE srsdrefid = recstatusrefid) END AS status
          FROM webset.def_classroomtype
         WHERE (1=1) ADD_SEARCH
         ORDER BY crtdesc
	";


	$list->title = "Classroom Type ";

	$list->addSearchField("ID", "(crtrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");

	$list->addColumn('ID')->sqlField('crtrefid');
	$list->addColumn("Classroom Type")->sqlField('crtdesc');
	$list->addColumn("Narrative")->sqlField('crtnarrsw')->type('switch');
	$list->addColumn("Current Record Status")->sqlField('status');

	$list->addURL = CoreUtils::getURL('./classroom_add.php');
	$list->editURL = CoreUtils::getURL('./classroom_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_classroomtype')
			->setKeyField('crtrefid')
			->applyListClassMode()
	);

	$list->deleteTableName = "webset.def_classroomtype";
	$list->deleteKeyField = "crtrefid";

	$list->printList();
?>
