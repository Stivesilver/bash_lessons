<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

	    $gskid = io::get('gskid');

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit Sentence Condition';
        $edit->setSourceTable('webset.disdef_bgb_ksaconditions', 'crefid');

        $edit->addGroup('General Information');

        $edit->addControl('Domain / Scope / KSA', 'select')
            ->sqlField('blksa')
            ->optionsCSS("color: AF_COL2")
            ->sql("
				SELECT gdskrefid,
			           " . IDEAParts::get('baselineArea') . ",
			           CASE
			               WHEN NOW() > domain.enddate
			                 OR NOW() > scope.enddate
			                 OR NOW() > ksa.enddate THEN 'red'
			               ELSE ''
			           END
		     	  FROM webset.disdef_bgb_goaldomainscopeksa ksa
		     	       INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
		     	       INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
			     WHERE domain.vndrefid = VNDREFID
			     ORDER BY 3, domain.gdsdesc, scope.gdssdesc, ksa.gdsksdesc
			")
	        ->value($gskid);

        $edit->addControl('Condition', 'edit')->sqlField('cdesc')->size(80)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');


        $edit->finishURL = CoreUtils::getURL('./ksa_condition.php', array('gskid' => $gskid));
        $edit->cancelURL = CoreUtils::getURL('./ksa_condition.php', array('gskid' => $gskid));

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    } else {

	    $gskid = io::get('gskid');

        $list = new ListClass();

        $list->title = 'Sentence Condition';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT condition.crefid,
				   " . IDEAParts::get('baselineArea') . ",
				   condition.cdesc,
				   CASE
		               WHEN NOW() > domain.enddate THEN 'N'
		               WHEN NOW() > scope.enddate  THEN 'N'
		               WHEN NOW() > ksa.enddate    THEN 'N'
		               WHEN NOW() > condition.enddate THEN 'N'
		               ELSE 'Y'
		           END as status
		      FROM webset.disdef_bgb_ksaconditions condition
            	   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON condition.blksa = ksa.gdskrefid
          	       INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
          	       INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
             WHERE domain.vndrefid = VNDREFID
               AND umrefid IS NULL
                   ADD_SEARCH
             ORDER BY 2, cdesc
		";

        $list->addSearchField('Domain / Scope / KSA', 'ksa.gdskrefid', 'select')
            ->sql("
				SELECT gdskrefid,
				  	   " . IDEAParts::get('baselineArea') . "
			      FROM webset.disdef_bgb_goaldomainscopeksa ksa
                       INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
	                   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
                 WHERE domain.vndrefid = VNDREFID
                   AND CASE WHEN NOW() > domain.enddate THEN 2 ELSE 1 END = 1
                   AND CASE WHEN NOW() > scope.enddate THEN 2 ELSE 1 END = 1
                   AND CASE WHEN NOW() > ksa.enddate THEN 2 ELSE 1 END = 1
                 ORDER BY 2
			")
            ->value($gskid);

        $list->addSearchField('Condition', "lower(cdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory('Status'))
            ->sqlField("
    			CASE
	              WHEN NOW() > domain.enddate THEN 'N'
	              WHEN NOW() > scope.enddate  THEN 'N'
	              WHEN NOW() > ksa.enddate    THEN 'N'
	              WHEN NOW() > condition.enddate THEN 'N'
	              ELSE 'Y'
	            END
	        ");

        $list->addColumn('Domain / Scope/ KSA', '50%');
        $list->addColumn('Condition', '30%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = CoreUtils::getURL('./ksa_condition.php', array('gskid' => $gskid));
        $list->editURL = CoreUtils::getURL('./ksa_condition.php', array('gskid' => $gskid));

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_ksaconditions')
                ->setKeyField('crefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
