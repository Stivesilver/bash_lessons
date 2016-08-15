<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Unarchive Eval Proc Form";
	
	$list->SQL = "
		SELECT ev.frefid,
		       stdlnm || ' ' || stdfnm AS uname,
		       dev.form_title,
		       ev.lastuser,
		       ev.lastupdate
		  FROM webset.es_std_evalproc_forms ev
		       INNER JOIN webset.es_disdef_evalforms AS dev ON (dev.efrefid = ev.evalforms_id)
		       INNER JOIN webset.es_std_evalproc AS evpr ON (ev.evalproc_id = evpr.eprefid)
		       LEFT JOIN webset.sys_teacherstudentassignment ts ON (evpr.stdrefid = ts.tsrefid)
		       LEFT JOIN webset.dmg_studentmst dmg ON (dmg.stdrefid = ts.stdrefid)
		 WHERE archived = 'Y'
		   AND evalproc_id IS NOT NULL
		   ADD_SEARCH
		 ORDER BY UPPER(stdlnm), UPPER(stdfnm), ev.frefid
	";

	$list->addSearchField(FFStudentName::factory());

	$list->addColumn("Student")->sqlField('uname');
	$list->addColumn("Form Description")->sqlField('form_title');
	$list->addColumn("Archived By")->sqlField('lastuser');
	$list->addColumn("Archived On")->sqlField('lastupdate');

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.es_std_evalproc_forms')
		->setKeyField('frefid')
		->applyListClassMode()
	);

	$list->addRecordsProcess('Unarchive')
		->message('Do you really want to unarchive deleted Deleted Eval Proc Form?')
		->url(CoreUtils::getURL('eval_form_arc.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

?>
