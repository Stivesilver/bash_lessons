<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted Evaluation Process";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT eprefid,
	           stdlnm || ' ' || stdfnm,
               essrtdescription,
	           to_char(eval.lastupdate,'MM-DD-YYYY'),
	           eval.lastuser
	      FROM webset.es_std_evalproc eval
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = eval.delrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
               INNER JOIN webset.es_statedef_reporttype ON essrtrefid = ev_type
	     WHERE eval.stdrefid is NULL
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), eval.lastupdate desc
	    ";
	    
	$list->addSearchField(FFStudentName::factory());
	$list->addColumn("Student", "");
	$list->addColumn("Evaluation", "");
	$list->addColumn("Deleted On", "");
	$list->addColumn("Deleted By", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.es_std_evalproc')
              ->setKeyField('eprefid')
              ->applyListClassMode()
      );
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore Evaluation Process?')
		->url(CoreUtils::getURL('evalproc_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 

	$list->printList();
  
?>
