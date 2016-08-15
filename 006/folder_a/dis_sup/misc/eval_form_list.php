<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted Eval Proc Form";
	$list->multipleEdit 	= "no";
	$list->SQL = "
		SELECT ev.frefid,
		       stdlnm || ' ' || stdfnm AS uname,
		       '<b>' || dev.form_title || CASE archived WHEN 'Y' then ' - Archived' ELSE '' END AS fdesc,
		       ev.lastuser,
		       ev.lastupdate
		  FROM webset.es_std_evalproc_forms ev
		  	   LEFT JOIN webset.es_disdef_evalforms AS dev ON (dev.efrefid = ev.evalforms_id)
		  	   INNER JOIN webset.es_std_evalproc AS evpr ON (ev.deleted_id = evpr.eprefid)
		       LEFT JOIN webset.sys_teacherstudentassignment ts ON (evpr.stdrefid = ts.tsrefid)
		       LEFT JOIN webset.dmg_studentmst dmg ON (dmg.stdrefid = ts.stdrefid)
		 WHERE evalproc_id IS NULL
		   ADD_SEARCH
		 ORDER BY upper(stdlnm), upper(stdfnm), ev.frefid
	";

	$list->addSearchField(FFStudentName::factory());

	$list->addColumn("Student")->sqlField('uname');
	$list->addColumn("Form Description")->sqlField('fdesc');
	$list->addColumn("Deleted By")->sqlField('lastuser');
	$list->addColumn("Deleted On")->sqlField('lastupdate');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_evalproc_forms')
			->setKeyField('frefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Restore')
		->message('Do you really want to restore deleted Deleted Eval Proc Form?')
		->url(CoreUtils::getURL('eval_form_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

?>
