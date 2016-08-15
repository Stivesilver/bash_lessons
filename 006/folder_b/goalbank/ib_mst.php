<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {


        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit Item';
        $edit->setSourceTable('webset.disdef_bgb_itemsbank', 'ibmrefid');

        $edit->addGroup('General Information');

        $edit->addControl('Category', 'select')
            ->sqlField('ibcrefid')
            ->optionsCSS("color: AF_COL2")
            ->sql("
				SELECT ibcrefid,
			           CASE WHEN NOW() > enddate THEN 'Inactive - ' ELSE '' END || 
			           ibcdesc,
			           CASE WHEN NOW() > enddate THEN 'red' ELSE '' END			           
		     	  FROM webset.disdef_bgb_itembank_cat
			     WHERE vndrefid = VNDREFID 
			     ORDER BY CASE WHEN NOW() > enddate THEN 2 ELSE 1 END, ibcdesc 
			");

        $edit->addControl('Domain / Scope', 'select')
            ->sqlField('scoperefid')
            ->optionsCSS("color: AF_COL2")
            ->sql("
				SELECT scope.gdsrefid,
			           CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 'Inactive - ' ELSE '' END || 
			           domain.gdsdesc || ' / ' || scope.gdssdesc,
			           CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 'red' ELSE '' END			           
		     	  FROM webset.disdef_bgb_goaldomainscope scope		     	       
		     	       INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid		     	       
			     WHERE domain.vndrefid = VNDREFID 
			     ORDER BY CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 2 ELSE 1 END, domain.gdsdesc, scope.gdssdesc 
			");

        $edit->addControl('Non-Tech', 'edit')->sqlField('ibmntdesc')->size(50)->req();
        $edit->addControl('Tech', 'edit')->sqlField('ibmtdesc')->size(50);
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');


        $edit->finishURL = 'ib_mst.php';
        $edit->cancelURL = 'ib_mst.php';

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Items Bank';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT ibmrefid,
                   ibcdesc,
                   domain.gdsdesc || ' / ' || scope.gdssdesc,
                   ibmntdesc,
                   ibmtdesc,
                   CASE 
                       WHEN NOW() > cat.enddate THEN 'N'
		               WHEN NOW() > domain.enddate THEN 'N' 
		               WHEN NOW() > scope.enddate  THEN 'N'		                
		               WHEN NOW() > dtl.enddate THEN 'N'
		               ELSE 'Y' 
		           END as status
			  FROM webset.disdef_bgb_itemsbank dtl
  	               INNER JOIN webset.disdef_bgb_itembank_cat cat ON cat.ibcrefid = dtl.ibcrefid
	               INNER JOIN webset.disdef_bgb_goaldomainscope scope ON scope.gdsrefid = scoperefid
	               INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
			 WHERE (1=1) ADD_SEARCH 
			   AND dtl.vndRefID = VNDREFID
             ORDER BY 2, 3, ibmntdesc 						     
		";

        $list->addSearchField('Category', 'dtl.ibcrefid', 'select')
            ->sql("
				SELECT ibcrefid,ibcdesc
            	  FROM webset.disdef_bgb_itembank_cat
                 WHERE vndRefID = VNDREFID
                 ORDER BY ibcdesc
			");

        $list->addSearchField('Domain / Scope', 'scoperefid', 'select')
            ->sql("
				SELECT gdsrefid,
				  	   domain.gdsdesc || ' / ' || scope.gdssdesc
			      FROM webset.disdef_bgb_goaldomain domain                       
			      	   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON domain.gdrefid = scope.gdrefid
                 WHERE domain.vndrefid = VNDREFID
                   AND CASE WHEN NOW() > domain.enddate THEN 2 ELSE 1 END = 1
                   AND CASE WHEN NOW() > scope.enddate THEN 2 ELSE 1 END = 1
                 ORDER BY 2
			");

        $list->addSearchField('Non-Tech', "lower(ibmntdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");
        $list->addSearchField('Tech', "lower(ibmtdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory('Status'))
            ->sqlField("
    			CASE 
	              WHEN NOW() > cat.enddate THEN 'N'
		          WHEN NOW() > domain.enddate THEN 'N' 
		          WHEN NOW() > scope.enddate  THEN 'N'		                
		          WHEN NOW() > dtl.enddate THEN 'N'
	              ELSE 'Y' 
	            END
	        ");

        $list->addColumn('Category');
        $list->addColumn('Domain / Scope');
        $list->addColumn('Non-Tech');
        $list->addColumn('Tech');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'ib_mst.php';
        $list->editURL = 'ib_mst.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_itemsbank')
                ->setKeyField('ibmrefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
