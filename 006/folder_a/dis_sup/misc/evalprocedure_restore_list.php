<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted Eval Procedures";
	$list->multipleEdit 	= "no";
	$list->SQL = "
		SELECT ev.shsdrefid,
		       stdlnm || ' ' || stdfnm AS uname,
		       CASE WHEN lower(hspdesc) LIKE '%other%' THEN COALESCE(test_name, hspdesc) ELSE hspdesc END AS as_name,
		       ev.lastuser,
		       ev.lastupdate
		  FROM webset.es_std_scr ev
		  	   INNER JOIN webset.es_scr_disdef_proc ass ON ev.hsprefid = ass.hsprefid
		  	   INNER JOIN webset.es_std_evalproc AS evpr ON (ev.deleted_id = evpr.eprefid)
		       LEFT JOIN webset.sys_teacherstudentassignment ts ON (evpr.stdrefid = ts.tsrefid)
		       LEFT JOIN webset.dmg_studentmst dmg ON (dmg.stdrefid = ts.stdrefid)
		 WHERE ev.eprefid IS NULL
		   ADD_SEARCH
		 ORDER BY upper(stdlnm), upper(stdfnm), ev.shsdrefid
	";

	$list->addSearchField(FFStudentName::factory());

	$list->addColumn("Student")->sqlField('uname');
	$list->addColumn("Form Description")->sqlField('as_name');
	$list->addColumn("Deleted By")->sqlField('lastuser');
	$list->addColumn("Deleted On")->sqlField('lastupdate')->type('date');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_scr')
			->setKeyField('shsdrefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Restore')
		->message('Do you really want to restore deleted Deleted Eval Proc Form?')
		->url(CoreUtils::getURL('eval_procedure_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

?>
