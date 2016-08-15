<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted XML Forms";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT sfrefid,
	           stdlnm || ' ' || stdfnm,
               form_name,
	           forms.lastuser,
               forms.lastupdate
	      FROM webset.std_forms_xml forms
               INNER JOIN webset.std_iep_year iep ON iepyear = siymrefid
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = iep.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	           INNER JOIN webset.statedef_forms_xml ON forms.frefid = webset.statedef_forms_xml.frefid
	     WHERE forms.stdrefid is NULL
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), forms.lastupdate desc
	    ";
	    
	$list->addSearchField("Form", "lower(form_name)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addColumn("Student", "");
	$list->addColumn("Form", "");
	$list->addColumn("Deleted By", "");
	$list->addColumn("Deleted On", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.std_forms_xml')
              ->setKeyField('sfrefid')
              ->applyListClassMode()
      );
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore deleted XML Forms?')
		->url(CoreUtils::getURL('xml_undelete.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 
	$list->printList();  	

?>
