<?php

	Security::init();

	$refID = io::geti('RefID');
	$dskey = io::get('dskey');
 	$edit = new editClass("edit1", $refID);
 	$stateRefID = VNDState::factory()->id;
 	$ds = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$dskey = io::get('dskey');
	$mainUrl = CoreUtils::getURL('srv_srv_list.php', array('dskey' => $dskey));
	$student = new IDEAStudent($tsRefID);
	
	$edit->setSourceTable('webset.std_srv_sped', 'ssmrefid'); 
 	
 	$edit->title = "Add/Edit Services";
 	$edit->finishURL = $edit->cancelURL = $mainUrl;

    $sql1 = "
    	SELECT stsRefID, COALESCE(stsCode || ' - ' , '') || stsDesc AS stsDescr
          FROM webset.statedef_services_sped
         WHERE scRefID = $stateRefID
           AND (recdeactivationdt>now() or recdeactivationdt is Null)
         ORDER BY stsCode, CASE lower(stsDesc) WHEN 'other' THEN 'z' ELSE stsDesc END
        ";

    $sql2 = "
    	SELECT sfrefid, sfdesc
          FROM webset.disdef_frequency
         WHERE (enddate>now() or enddate is Null)
           AND vndrefid = VNDREFID
         ORDER BY sfdesc
        ";  

    $sql3 = "
    	SELECT crtrefid, crtdesc
          FROM webset.disdef_location
         WHERE (enddate>now() or enddate is Null)
           AND vndrefid = VNDREFID
         ORDER BY CASE substring(lower(crtdesc), 1, 5)  WHEN 'other' THEN 'z' ELSE crtdesc END
        ";
       
    $edit->addGroup('General Information');
    $edit->addControl("Order #", "INTEGER")
	    ->sqlField('order_num')
	    ->value(db::execSQL("
    	 	  	  SELECT MAX(order_num) FROM webset.std_srv_sped
    	 	  	   WHERE iepyear = $stdIEPYear
    	 	  	     AND stdrefid = $tsRefID
    	 	  ")->getOne() + 1);
    	 	  
    $edit->addControl("Service", "SELECT")
	    ->name('service')
	    ->sql($sql1)
	    ->sqlField('stsRefID')
	    ->emptyOption(true)
	    ->req(true);
    	 
    $edit->addControl("Specify")
	    ->sqlField('stsother')
	    ->size(50)
	    ->showIf('service', db::execSQL("
                                  SELECT stsrefid
          						  	FROM webset.statedef_services_sped
                                   WHERE stsdesc ILIKE 'other%'
                                  ")->indexAll());
    $edit->addControl("Medicaid Code", "HIDDEN")
	    ->sqlField('BCPDesc');
    $edit->addControl("Beginning Date", "DATE")
	    ->sqlField('ssmBegDate')
	    ->value($student->getDate('stdiepmeetingdt'));
    $edit->addControl("Ending Date", "DATE")
	    ->sqlField('ssmEndDate')
	    ->value($student->getDate('stdcmpltdt'));
    
    $edit->addGroup('Total Service per Week');
    $edit->addControl("Hours", "FLOAT")
	    ->sqlField('hours');
    $edit->addControl("Minutes", "INTEGER")
	    ->sqlField('minutes');
 	$edit->addControl("Frequency", "SELECT")
	    ->sql($sql2)
	    ->sqlField('ssmFreq');
    
    $edit->addGroup('Location');
    $edit->addControl("Location", "SELECT")
	    ->name('location')
	    ->sql($sql3)
	    ->sqlField('ssmClassType');
    $edit->addControl("Specify Location", "EDIT")
	    ->sqlField('ssmclasstypenarr')
	    ->showIf('location', db::execSQL("
                                  SELECT crtrefid
          						  	FROM webset.disdef_location
                                   WHERE crtdesc ILIKE 'other%'
                                  ")->indexAll());
    $edit->addControl("Position Responsible", "EDIT")
	    ->sqlField('ssmteacherother');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl("Last User", "PROTECTED")
	    ->value(SystemCore::$userUID)
	    ->sqlField('LastUser');
    $edit->addControl("Last Update", "PROTECTED")
	    ->value(date("m-d-Y H:i:s"))
	    ->sqlField('LastUpdate');
    $edit->addControl("tsRefID", "HIDDEN")
	    ->value($tsRefID)
	    ->sqlField('stdrefid');
    $edit->addControl("tsRefID", "HIDDEN")
	    ->value($stdIEPYear)
	    ->sqlField('iepyear');
    	 
    $edit->printEdit(); 
  
?>
