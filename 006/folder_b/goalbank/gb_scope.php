<?php

	Security::init();

	$RefID = io::get('RefID');

	if ($RefID > 0 or $RefID == '0') {


		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit Scope';
		$edit->setSourceTable('webset.disdef_bgb_goaldomainscope', 'gdsrefid');

		$edit->addTab('General Information');
		$edit->addControl('Domain', 'select')
			->sqlField('gdrefid')
			->optionsCSS("color: AF_COL2")
			->sql("
				SELECT gdrefid,
			           CASE WHEN NOW() > enddate THEN 'Inactive - ' ELSE '' END || gdsdesc,
			           CASE WHEN NOW() > enddate THEN 'red' ELSE '' END
		     	  FROM webset.disdef_bgb_goaldomain
			     WHERE vndrefid = VNDREFID
			     ORDER BY CASE WHEN NOW() > enddate THEN 2 ELSE 1 END, gdsdesc
		");
		$edit->addControl('Scope', 'edit')->sqlField('gdssdesc')->size(80)->req();
		$edit->addControl('Description', 'textarea')->sqlField('gdsdesc');
		$edit->addControl('Items Bank', 'select')
			->sqlField('itemsbank')
			->data(
				array(
					0 => 'No',
					1 => 'Yes'
				)
		);

		$edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		if ($RefID > 0) {
			$edit->addTab('State Standards');
			$edit->addIFrame(CoreUtils::getURL('gb_scope_standart.php', array('scope_refid' => $RefID)))
				->height('500px');
		}

		$edit->finishURL = 'gb_scope.php';
		$edit->cancelURL = 'gb_scope.php';

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = 'Scopes';

		$list->showSearchFields = true;

		$list->SQL = "
			SELECT gdsrefid,
                   domain.gdsdesc,
                   gdssdesc,
				   (SELECT count(1)
                      FROM webset.disdef_bgb_standart_scope
                     WHERE scope_refid::integer = gdsrefid) || ' items',
                   CASE
                       WHEN NOW() > domain.enddate THEN 'N'
                       WHEN NOW() > scope.enddate  THEN 'N'
                       ELSE 'Y'
                   END as status
              FROM webset.disdef_bgb_goaldomainscope scope
                   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
             WHERE domain.vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY domain.gdsdesc,gdssdesc
		";

		$list->addSearchField('Domain', 'scope.gdrefid', 'select')
			->sql("
				SELECT gdrefid,
			           gdsdesc
		     	  FROM webset.disdef_bgb_goaldomain
			     WHERE vndrefid = VNDREFID
			       AND CASE WHEN NOW() > enddate THEN 2 ELSE 1 END = 1
			     ORDER BY gdsdesc
			");

		$list->addSearchField('Scope', "lower(gdssdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");
		$list->addSearchField(FFIDEAStatus::factory('Status'))
			->sqlField("
    			CASE
	              WHEN NOW() > domain.enddate THEN 'N'
	              WHEN NOW() > scope.enddate  THEN 'N'
	              ELSE 'Y'
	            END
	        ");

		$list->addColumn('Domain', '40%');
		$list->addColumn('Scope', '40%');
		$list->addColumn('State Standards')
			->type('tablehint')
			->param("
				SELECT ssdurl,
					   ssddesc
				  FROM webset.disdef_bgb_standart_scope
				 WHERE scope_refid::integer = AF_REFID
				 ORDER BY ssddesc
            ");
		$list->addColumn('Active')->type('switch')->sqlField('status');

		$list->addURL = 'gb_scope.php';
		$list->editURL = 'gb_scope.php';

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_bgb_goaldomainscope')
				->setKeyField('gdsrefid')
				->applyListClassMode()
				->setNesting('webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid', 'gdsrefid', 'webset.disdef_bgb_goaldomainscope', 'gdsrefid')
				->setNesting('webset.disdef_bgb_ksaksgoalactions', 'gdskgarefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
				->setNesting('webset.disdef_bgb_scpksaksgoalcontent', 'gdskgcrefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
				->setNesting('webset.disdef_bgb_ksaconditions', 'crefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
				->setNesting('webset.disdef_bgb_ksacriteria', 'crrefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
		);

		$list->printList();
	}
?>
