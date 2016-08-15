<?php

    Security::init();

  	$dskey   = io::get('dskey');
	$ds 	 = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID'); 
	$refID 	 = io::geti('RefID');
	$areaID  = io::geti('area_id');
	$edit    = new editClass("edit1", $refID);

	$edit->setSourceTable('webset.std_general', 'refid');

    $edit->title = 'Area of Concern';

	$edit->addGroup("General Information");
	
	$edit->addControl("Order #", "integer")
		 ->sqlField('order_num')
		 ->size(4)
		 ->value(
			db::execSQL("
                SELECT max(order_num)
                  FROM webset.std_general
                 WHERE stdrefid = " . $tsRefID . "
                   AND area_id = " . $areaID
            )->getOne() + 1);

	$edit->addControl(FFInputDropList::factory('Area of Concern')
		->sqlField('txt08')
		->dropListSQL("
            SELECT refid,
                   validvalue
              FROM webset.disdef_validvalues
             WHERE vndrefid = VNDREFID
               AND valuename = 'ID_SLD_Concern_Area'
               AND (glb_enddate IS NULL or now()< glb_enddate)
             ORDER BY sequence_number, validvalue ASC
        "))
		->highlightField(false)
		->width('400px');

	$edit->addControl("Date", "date")
		 ->sqlField('dat01');

	$edit->addControl(FFInputDropList::factory('Name of Assessment')
		->sqlField('txt01')
		->dropListSQL("
            SELECT scrdesc,
                   hspdesc
              FROM webset.es_scr_disdef_proc
                   INNER JOIN webset.es_statedef_screeningtype ON webset.es_statedef_screeningtype.scrrefid = webset.es_scr_disdef_proc.screenid
             WHERE vndrefid = VNDREFID
             ORDER BY scrdesc, hspdesc
        "))
		->highlightField(false)
		->width('400px');

	$edit->addControl("Subtest(s)", "textarea")
		->sqlField('txt02')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
        
	$edit->addControl("SS", "textarea")
		->sqlField('txt03')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
        
	$edit->addControl("%ile", "textarea")
		->sqlField('txt04')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);

	$edit->addControl(FFInputDropList::factory('Evaluator/Title')
		->sqlField('txt05')
		->dropListSQL("
            SELECT umrefid, umlastname || ', ' || umfirstname, umtitle
              FROM public.sys_usermst
             WHERE vndrefid = VNDREFID
               AND um_internal
             ORDER BY UPPER(umlastname), UPPER(umfirstname)
        "))
		->highlightField(false)
		->width('400px');
		
	$edit->addControl("Description of assessment measure, validity statement, and interpretive information", "textarea")
		->sqlField('txt06')
		->css("WIDTH", "100%")
		->css("HEIGHT", "80px")
		->autoHeight(true);
              
	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")
		 ->value($_SESSION["s_userUID"])
		 ->sqlField('lastuser');
		
	$edit->addControl("Last Update", "protected")
		 ->value(date("m-d-Y H:i:s"))
		 ->sqlField('lastupdate');
		
	$edit->addControl("tsRefID", "hidden")
		 ->value($tsRefID)
		 ->sqlField('stdrefid');

	$edit->addControl("area_id", "hidden")
		 ->value($areaID)
		 ->sqlField('area_id');
		 
    $edit->firstCellWidth = "25%";

	$edit->printEdit();
  
?>
