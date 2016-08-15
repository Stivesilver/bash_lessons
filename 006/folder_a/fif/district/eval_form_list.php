<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted 504 Form";
	$list->multipleEdit 	= "no";
	$list->SQL = "
		SELECT s.sfrefid,
		       stdlnm || ' ' || stdfnm AS uname,
		       his.initdate,
		       f.fname,
		       s.lastuser,
		       s.lastupdate
		  FROM webset.std_fif_forms s
		       INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
		       INNER JOIN webset.std_fif_history AS his ON (s.deleted_id = his.hisrefid)
		       LEFT JOIN webset.dmg_studentmst dmg ON (dmg.stdrefid = his.stdrefid)
		 WHERE his.deleted_id IS NULL ADD_SEARCH
		 ORDER BY upper(stdlnm), upper(stdfnm), f.frefid
	";

	$list->addSearchField(FFStudentName::factory());

	$list->addColumn("Student")->sqlField('uname');
	$list->addColumn("504 Initial Referral Date")->sqlField('initdate')->type('date');
	$list->addColumn("Form Description")->sqlField('fname');
	$list->addColumn("Deleted By")->sqlField('lastuser');
	$list->addColumn("Deleted On")->sqlField('lastupdate');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_fif_forms')
			->setKeyField('sfrefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Restore')
		->message('Do you really want to restore deleted Deleted Form?')
		->url(CoreUtils::getURL('eval_form_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

?>
