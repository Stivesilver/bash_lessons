<?php

	Security::init();
	
	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Archived PDF Forms";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT smfcrefid,
	           stdlnm || ' ' || stdfnm,
	           to_char(smfcdate,'MM-DD-YYYY'),
               COALESCE(mfcdoctitle, uploaded_name),
	           forms.lastuser
	      FROM webset.std_forms forms
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = forms.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	           LEFT OUTER JOIN webset.statedef_forms ON forms.mfcrefid = webset.statedef_forms.mfcrefid
	     WHERE archived = 'Y'
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), forms.lastupdate desc
	    ";
	    
	$list->addSearchField(FFStudentName::factory());  
	$list->addSearchField("Form");  
	$list->addColumn("Student", "");
	$list->addColumn("Archive Date", "");
	$list->addColumn("Form", "");
	$list->addColumn("Archived By", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.std_forms')
              ->setKeyField('smfcrefid')
              ->applyListClassMode()
      );
    $list->addRecordsProcess('Unarchive')
		->message('Do you really want to unarchive archived PDF forms?')
		->url(CoreUtils::getURL('pdf_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 

	$list->printList(); 	

?>
