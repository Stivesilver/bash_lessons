<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $cat_id = 4;

    $SQL = "
    	SELECT tsnsrefid 
          FROM webset.std_tsn 
               INNER JOIN webset.statedef_tsn ON tsnstatedefrefid = tsnrefid
         WHERE tsncatrefid = " . $cat_id . "
           AND stdrefid = " . $tsRefID . "
    ";

    $result = db::execSQL($SQL);
    if (!$result->EOF) {
        $tsnsrefid = $result->fields[0];
    } else {
        $tsnsrefid = 0;
    }

    $edit = new EditClass("edit1", $tsnsrefid);

    $edit->title = 'Graduation Information';

    $edit->setSourceTable('webset.std_tsn', 'tsnsrefid');

    $edit->addGroup('General Information');

    $edit->addControl('Graduation Information', 'select_radio')
        ->sqlField('tsnstatedefrefid')
        ->name('tsnstatedefrefid')
        ->sql("
        	SELECT tsnrefid, tsndesc
        	  FROM webset.statedef_tsn
             WHERE tsncatrefid = " . $cat_id . "
               AND screfid = " . VNDState::factory()->id . "
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)  
             ORDER BY tsndesc
        ")
        ->breakRow();

    $edit->addControl('Details', 'textarea')
        ->sqlField('tsnnarr')
        ->name('tsnnarr')
        ->hideIf('tsnstatedefrefid', db::execSQL("
                                  		SELECT tsnrefid
        								  FROM webset.statedef_tsn
							             WHERE COALESCE(tsnnarrsw, 'Y') = 'N'
                                     ")->indexAll())
        ->sql("
            SELECT tsndefaultnarr 
              FROM webset.statedef_tsn
             WHERE tsnrefid = NULLIF('VALUE_01','')::integer
        ")
        ->tie('tsnstatedefrefid');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_tsn')
            ->setKeyField('tsnsrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?> 