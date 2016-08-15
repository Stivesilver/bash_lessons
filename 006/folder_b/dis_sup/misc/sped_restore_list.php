<?php

	Security::init();
	
	$list = new listClass();

	$list->title            = "Restore Deleted Sp Ed Enrollments";
	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->multipleEdit     = "no";

	$list->SQL = "
		SELECT tsrefid,
	           stdlnm || ' ' || stdfnm,
	           to_char(ts.lastupdate,'MM-DD-YYYY'),
	           ts.lastuser
	      FROM webset.sys_teacherstudentassignment ts
    	       INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdschoolyear
	     WHERE ts.stdrefid is NULL
	           ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM)
	";

	$list->addSearchField(FFStudentName::factory());
	$list->addColumn("Student", "", "", "", "");
	$list->addColumn("Deleted Date", "", "", "", "");
	$list->addColumn("Deleted By", "", "", "", "");
   	$list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.sys_teacherstudentassignment')
              ->setKeyField('tsrefid')
              ->applyListClassMode()
      );  
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore this students?')
		->url(CoreUtils::getURL('sped_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);
	$list->printList();
		  
?>