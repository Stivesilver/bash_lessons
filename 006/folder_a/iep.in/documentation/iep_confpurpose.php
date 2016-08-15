<?php

    Security::init();
	IDEAFormat::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');
	$set_id = IDEAFormat::get('id');
	$set_ini = IDEAFormat::getIniOptions();

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'IEP Purpose of Conference';

    $edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsRefID');

    $edit->addGroup('General Information');

    $edit->addControl('IEP Purpose of Conference', 'select_check')
        ->name('purpose')
        ->value(
            implode(',', db::execSQL("
        		SELECT siepcprefid
        		  FROM webset.std_in_iepconfpurpose
        		 WHERE stdrefid = " . $tsRefID . "
        	")->indexCol(0))
        )
        ->sql("
			SELECT siepcprefid,
			       siepcpdesc
			  FROM webset.statedef_iepconfpurpose
		 	 WHERE screfid = " . VNDState::factory()->id . "
			   AND COALESCE(set_id, " . $set_id . ") = " . $set_id . "
			ORDER BY siep_seq, siepcprefid
        ")
        ->breakRow(true);

    $edit->addControl('Specify')
        ->name('other')
        ->value(
            db::execSQL("
        		SELECT sicpnarrative
        		  FROM webset.std_in_iepconfpurpose AS t0
                       INNER JOIN webset.statedef_iepconfpurpose AS t1 ON t1.siepcprefid = t0.siepcprefid
        		 WHERE stdrefid = " . $tsRefID . "
        		   AND LOWER(siepcpdesc) LIKE '%other%'
        	")->getOne()
        )
        ->showIf('purpose', db::execSQL("
				SELECT siepcprefid
				  FROM webset.statedef_iepconfpurpose
				 WHERE LOWER(siepcpdesc) LIKE '%other%'
            ")->indexAll())
        ->size(50);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected');
    $edit->addControl('Last Update', 'protected');

    $edit->setPostsaveCallback('savePurpose', 'iep_confpurpose.inc.php');
    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
    $edit->firstCellWidth = '30%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_in_iepconfpurpose')
            ->setKeyField('stdrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
	
	print UIMessage::factory($set_ini["in_purpose_conference_statement"], UIMessage::NOTE)->toHTML();
?>
