<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_goaldomain', 'gdrefid');

        $edit->title = 'Add/Edit Domain';

        $edit->addTab('General Information');
        $edit->addControl('Domain', 'edit')->sqlField('gdsdesc')->size(70)->req();
        $edit->addControl('Description', 'textarea')->sqlField('gddesc');
        $edit->addControl(FFSwitchYN::factory('Print on IEP'))->sqlField('prn_iep_sq');
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		if ($RefID > 0) {
			$edit->addTab('State Standards');
			$edit->addIFrame(CoreUtils::getURL('gb_domain_standart.php', array('domain_refid' => $RefID)))
				->height('500px');
		}

        $edit->finishURL = 'gb_domain.php';
        $edit->cancelURL = 'gb_domain.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Domains';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT gdrefid,
			       gdsdesc,
				   (SELECT count(1)
                      FROM webset.disdef_bgb_standart_domain
                     WHERE domain_refid::integer = gdrefid) || ' items',
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_goaldomain
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH
		     ORDER BY gdsdesc
		";

        $list->addSearchField('Domain', "lower(gdsdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Domains');
		$list->addColumn('State Standards')
            ->type('tablehint')
            ->param("
				SELECT ssdurl,
					   ssddesc
				  FROM webset.disdef_bgb_standart_domain
				 WHERE domain_refid::integer = AF_REFID
				 ORDER BY ssddesc
            ");

        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'gb_domain.php';
        $list->editURL = 'gb_domain.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_goaldomain')
                ->setKeyField('gdrefid')
                ->applyListClassMode()
	            ->setNesting('webset.disdef_bgb_goaldomainscope', 'gdsrefid', 'gdrefid', 'webset.disdef_bgb_goaldomain', 'gdrefid')
	            ->setNesting('webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid', 'gdsrefid', 'webset.disdef_bgb_goaldomainscope', 'gdsrefid')
	            ->setNesting('webset.disdef_bgb_ksaksgoalactions', 'gdskgarefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_scpksaksgoalcontent', 'gdskgcrefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_ksaconditions', 'crefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_ksacriteria', 'crrefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
        );

        $list->printList();
    }
?>
