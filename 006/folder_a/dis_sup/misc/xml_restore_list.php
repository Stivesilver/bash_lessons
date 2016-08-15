<?php

	Security::init();
	
	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Archived XML Forms";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT sfrefid,
	           stdlnm || ' ' || stdfnm,
	           to_char(forms.lastupdate,'MM-DD-YYYY'),
               form_name,
	           forms.lastuser
	      FROM webset.std_forms_xml forms
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = forms.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	           INNER JOIN webset.statedef_forms_xml ON forms.frefid = webset.statedef_forms_xml.frefid
	     WHERE archived = 'Y'
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), forms.lastupdate desc
	    ";
	    
	    $list->addSearchField(FFStudentName::factory());
        $list->addSearchField("Form", "lower(form_name)  like '%' || lower(ADD_VALUE)|| '%'");//"lower(form_name)  like '%' || lower(ADD_VALUE)|| '%'", "TEXT", "", "", "", "");
        $list->addColumn("Student", "");
	    $list->addColumn("Archive Date", "");
	    $list->addColumn("Form", "");
	    $list->addColumn("Archived By", "");
	    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.es_std_evalproc')
              ->setKeyField('eprefid')
              ->applyListClassMode()
      	);
	    $list->addRecordsProcess('Restore')
			->message('Do you really want to restore archived XML Forms?')
			->url(CoreUtils::getURL('xml_restore.ajax.php'))
			->type(ListClassProcess::DATA_UPDATE)
	        ->progressBar(false); 

		$list->printList();
        
?>
