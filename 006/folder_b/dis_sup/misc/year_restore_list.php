<?php
  
	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted IEP Year";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT siymrefid,
	           stdlnm || ' ' || stdfnm,
               TO_CHAR(siymiepbegdate, 'mm-dd-yyyy'),
               TO_CHAR(siymiependdate, 'mm-dd-yyyy'),
	           iep.lastuser,
               TO_CHAR(iep.lastupdate, 'mm-dd-yyyy')
	      FROM webset.std_iep_year iep
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = iep.dsyrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	     WHERE iep.stdrefid is NULL
	           AND dmg.vndrefid = VNDREFID
	           ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), iep.lastupdate desc
	    ";
	    
	$list->addSearchField(FFStudentName::factory());
	$list->addColumn("Student", "");
	$list->addColumn("Start Date", "");
	$list->addColumn("End Date", "");
	$list->addColumn("Deleted By", "");
	$list->addColumn("Deleted On", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.es_std_evalproc')
              ->setKeyField('eprefid')
              ->applyListClassMode()
    );
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore deleted IEP Year?')
		->url(CoreUtils::getURL('year_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 
	$list->printList();  
  
?>
