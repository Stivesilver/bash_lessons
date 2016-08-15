<?php

	Security::init();

	$list = new listClass();

	$list->title            = "Restore Disabled Evaluations";
	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->multipleEdit     = "no";
	$list->SQL 				= "
		SELECT esarefid,
	           stdlnm || ' ' || stdfnm,
               esaname,
	           to_char(eval.lastupdate,'MM-DD-YYYY'),
	           eval.lastuser
	      FROM webset.es_std_esarchived eval
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = eval.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	     WHERE deleted = 'Y'
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), eval.lastupdate desc
	     ";

	$list->addSearchField(FFStudentName::factory());
	$list->addColumn("Student", "");
	$list->addColumn("Evaluation", "");
	$list->addColumn("Archive Date", "");
	$list->addColumn("Archived By", "");
   	$list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.es_std_esarchived')
              ->setKeyField('esarefid')
              ->applyListClassMode()
      );
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore disabled Evaluations?')
		->url(CoreUtils::getURL('eval_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);
	$list->printList();

?>
