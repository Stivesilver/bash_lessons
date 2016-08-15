<?php
  
  	Security::init();
  
    $list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Disabled IEPs";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		SELECT sIEPMRefID,
	           stdlnm || ' ' || stdfnm,
	           to_char(sIEPMDocDate,'MM-DD-YYYY') as sIEPMDocDate,
	           CASE sIEPMTDesc is NULL
	               WHEN TRUE THEN CASE iep.siepmtrefid
	                              WHEN -1 THEN 'Exit Summary'
	                              WHEN -2 THEN 'Service Plan'
	                              ELSE 'Exit Summary'
	                              END
	           ELSE sIEPMTDesc END ||
	           CASE iep_status
	            WHEN 'I' then ' - <font color=red>disabled</red>'
	            ELSE ''
	           END,
	           to_char(iep.stdEnrollDT,'MM-DD-YYYY'),
	           iep.lastuser
	      FROM webset.std_iep iep
	           INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = iep.stdrefid
	           INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
	           LEFT OUTER JOIN webset.statedef_ieptypes ON iep.sIEPMTRefID = webset.statedef_ieptypes.sIEPMTRefId
	     WHERE iep_status = 'I'
           AND dmg.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY upper(stdLNM), upper(stdFNM), iep.lastupdate desc
	    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addColumn("Student", "");
	$list->addColumn("Archive Date");
	$list->addColumn("Type of IEP", "");
	$list->addColumn("IEP Initiation Date", "");
	$list->addColumn("Archived By", "");
    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable('webset.std_iep')
              ->setKeyField('siepmrefid')
              ->applyListClassMode()
      );
    $list->addRecordsProcess('Restore')
		->message('Do you really want to restore disabled IEPs?')
		->url(CoreUtils::getURL('iep_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false); 
	$list->printList();
  
?>
