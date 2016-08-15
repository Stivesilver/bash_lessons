<?php

      Security::init();

      $refId = io::geti('RefID');
      
      if ($refId > 0 || $refId == null) {
          $edit = new editClass('edit1', $refId);

		  $edit->SQL = "
		  	  SELECT sfdesc,
          	         seqnum,
                     enddate,
                     lastuser,
                     lastupdate,
                     vndrefid
	            FROM webset.def_spedfreq
	           WHERE sfrefid = $refId
	      ";

		  $edit->title     = "Add/Edit Frequency";
		  $edit->finishURL = "afrequency_list.php";
		  $edit->cancelURL = "afrequency_list.php";
		
		  $edit->addGroup('General Information');
		  $edit->addControl("Frequency",  "EDIT")->size(90);
		  $edit->addControl("Sequence",  "INTEGER")->size(10);
		  $edit->addControl("Deactivation Date", "DATE");
		  $edit->addGroup('Update Information', true);
		  $edit->addControl("Last User", "PROTECTED")->value($_SESSION["s_userUID"]);
		  $edit->addControl("Last Update", "PROTECTED")->value(date("m-d-Y H:i:s"));
          $edit->addControl("Vndrefid", "HIDDEN")->value($_SESSION["s_VndRefID"]);
		  $edit->printEdit();
	  }

?>

