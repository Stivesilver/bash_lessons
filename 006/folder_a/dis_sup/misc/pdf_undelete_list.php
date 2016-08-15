<?php

	Security::init();
	
	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted PDF Forms";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT smfcrefid,
	           stdlnm || ' ' || stdfnm,
	           to_char(smfcdate,'MM-DD-YYYY'),
               mfcdoctitle,
	           forms.lastuser,
               forms.lastupdate
	      FROM webset.std_forms forms
               INNER JOIN webset.std_iep_year iep ON iepyear = siymrefid
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = iep.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	           INNER JOIN webset.statedef_forms ON forms.mfcrefid = webset.statedef_forms.mfcrefid
	     WHERE forms.stdrefid is NULL
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), forms.lastupdate desc
	 	";
	
	$list->addSearchField(FFStudentName::factory());    
	$list->addSearchField("Form", "lower(mfcdoctitle)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addColumn("Student", "");
	$list->addColumn("Form Date", "");
	$list->addColumn("Form", "");
	$list->addColumn("Deleted By", "");
	$list->addColumn("Deleted On", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.std_forms')
              ->setKeyField('smfcrefid')
              ->applyListClassMode()
    );
    $list->addRecordsProcess('Unarchive')
		->message('Do you really want to restore deleted PDF Forms?')
		->url(CoreUtils::getURL('pdf_undelete.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 

	$list->printList(); 

?>
