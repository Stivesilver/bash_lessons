<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'Needed Transition Services';

        $list->SQL = "
            SELECT std.sntsrefid, 
                   sadesc || ' / ' || needs.sandesc || COALESCE(' - ' || sannarrative, ''), 
                   strategies.stdesc,
                   stnarrative,
                   agencynarrative
			  FROM webset.std_nts std
			       LEFT OUTER JOIN webset.statedef_nts_areaneeds needs ON std.sanrefid = needs.sanrefid 
			       LEFT OUTER JOIN webset.statedef_nts_area areas ON needs.sarefid = areas.sarefid 
			       LEFT OUTER JOIN webset.statedef_nts_strategy strategies ON std.strefid = strategies.stdrefid
			 WHERE std.stdrefid = " . $tsRefID . "
			 ORDER BY areas.sarefid, needs.sanrefid
        ";

        $list->addColumn('Area/Service');
        $list->addColumn('Strategy');
        $list->addColumn('Description of Service');
        $list->addColumn('Responsible Parties');

        $list->deleteTableName = "webset.std_nts";
        $list->deleteKeyField = "sntsrefid";

        $list->addURL = CoreUtils::getURL('srv_nts.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_nts.php', array('dskey' => $dskey));

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable($list->deleteTableName)
                ->setKeyField($list->deleteKeyField)
                ->applyListClassMode()
        );

        $list->addButton(
            IDEAFormat::getPrintButton(array('dskey' => $dskey))
        );

        $list->printList();
    } else {

        $edit = new EditClass("edit1", $RefID);

        $edit->title = 'Needed Transition Services';

        $edit->setSourceTable('webset.std_nts', 'sntsrefid');

        $edit->addGroup('General Information');

        $edit->addControl('Area', 'select')
            ->sqlField('sanrefid')
            ->name('sanrefid')
            ->sql("
               SELECT sanrefid, sadesc || ' / ' || needs.sandesc
			     FROM webset.statedef_nts_area areas
                      INNER JOIN webset.statedef_nts_areaneeds needs ON areas.sarefid = needs.sarefid
				WHERE needs.screfid =  " . VNDState::factory()->id . "
				  AND (areas.recdeactivationdt IS NULL OR NOW() < areas.recdeactivationdt)  
				  AND (needs.recdeactivationdt IS NULL OR NOW() < needs.recdeactivationdt)
                ORDER BY sadesc, sandesc
            ")
            ->req();

        $edit->addControl('Area Needs Narrative', 'textarea')
            ->sqlField('sannarrative')
            ->showIf('sanrefid', db::execSQL("
                                  SELECT sanrefid
                                    FROM webset.statedef_nts_areaneeds
                                   WHERE substring(lower(sandesc), 1, 5) = 'other'
                                     AND screfid =  " . VNDState::factory()->id . "
				                     AND (recdeactivationdt IS NULL OR NOW() < recdeactivationdt)
                                 ")->indexAll());

        $edit->addControl('Strategy', 'select')
            ->sqlField('strefid')
            ->sql("
               SELECT stdrefid, stdesc
				 FROM webset.statedef_nts_strategy
			    WHERE screfid = " . VNDState::factory()->id . "
			      AND (recdeactivationdt IS NULL OR NOW() < recdeactivationdt)  
                ORDER BY stdesc
            ")
            ->req();

        $edit->addControl('Description of Service', 'textarea')
            ->sqlField('stnarrative');

        $edit->addControl('Responsible Parties', 'textarea')
            ->sqlField('agencynarrative');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_nts.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_nts.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?> 