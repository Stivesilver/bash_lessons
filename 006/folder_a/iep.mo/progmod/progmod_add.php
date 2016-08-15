<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $student = new IDEAStudent($tsRefID);

    $edit = new EditClass("edit1", $RefID);

    $edit->title = 'Add/Edit Program Modifications and Accommodations';

    $edit->setSourceTable('webset.statedef_mod_acc', 'stsrefid');

    $edit->addGroup("General Information");
    $edit->addControl("Modification/Accommodation", "select")
        ->sqlField('stsrefid')
        ->name('stsrefid')
        ->sql("
            SELECT stsrefid, macdesc || ': ' || stsdesc
              FROM webset.statedef_mod_acc acc
                   INNER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = acc.macrefid
             WHERE acc.screfid = " . VNDState::factory()->id . "
               AND (recactivationdt IS NULL or now()< recactivationdt)
               " . ($RefID > 0 ? " AND stsrefid = " . $RefID : "") . "
             ORDER BY seq_num, stsseq, stsdesc
        ");

    $edit->addControl('Specify')
        ->name('stsrefid_other')
        ->showIf('stsrefid', db::execSQL("
                                  SELECT stsrefid
                                    FROM webset.statedef_mod_acc
                                   WHERE substring(lower(stsdesc), 1, 5) = 'other'
                                 ")->indexAll())
        ->size(50);

    $edit->addControl("Location", "select_check")
        ->name('malrefid')
        ->sql("
            SELECT malrefid, maldesc
              FROM webset.statedef_mod_acc_loc
             WHERE screfid = " . VNDState::factory()->id . "
               AND (recactivationdt IS NULL or now()< recactivationdt)
             ORDER BY seq_num, maldesc
        ")
        ->value(db::execSQL("
            SELECT plpgsql_recs_to_str('
                       SELECT malrefid::varchar AS column
                         FROM webset.std_progmod
                              INNER JOIN webset.statedef_mod_acc_loc ON val_id::int = malrefid
                        WHERE stdrefid = " . $tsRefID . "
                          AND typeofval = ''loc''
                          AND stsrefid = " . $RefID . "
                        ORDER BY seq_num, maldesc', ','
                   )
        ")->getOne())
        ->breakRow();

    $edit->addControl("Frequency", "select_check")
        ->name('esfumrefid')
        ->sql("
            SELECT esfumrefid, esfumdesc,
                   CASE esfumdesc
                   WHEN 'Daily'   THEN 1
                   WHEN 'Weekly'  THEN 2
                   WHEN 'Monthly' THEN 3
                   WHEN 'Other'   THEN 4
                   END
              FROM webset.statedef_esy_serv_freq_unit_of_measur
             WHERE screfid = " . VNDState::factory()->id . "
             AND (esfumdesc like 'Daily' OR
                  esfumdesc like 'Weekly' OR
                  esfumdesc like 'Monthly' OR
                  esfumdesc like 'Other')
             ORDER BY 3
        ")
        ->value(db::execSQL("
            SELECT plpgsql_recs_to_str('
                       SELECT malrefid::varchar AS column
                         FROM webset.std_progmod
                              INNER JOIN webset.statedef_mod_acc_loc ON val_id::int = malrefid
                        WHERE stdrefid = " . $tsRefID . "
                          AND typeofval = ''frq''
                          AND stsrefid = " . $RefID . "
                        ORDER BY seq_num, maldesc', ','
                   )
        ")->getOne())
        ->breakRow();

    $edit->addControl('Beginning Date', 'date')
        ->name('begdate')
        ->value($RefID > 0 ?
                db::execSQL("
                               SELECT substring(val from '......(....)') || '/' || substring(val from '(.....).....') AS column
                                 FROM webset.std_progmod
                                WHERE stdrefid = " . $tsRefID . "
                                  AND typeofval = 'beg'
                                  AND stsrefid = " . $RefID . "
                ")->getOne() :
                $student->getDate('stdenrolldt')
    );

    $edit->addControl('Ending Date', 'date')
        ->name('enddate')
        ->value($RefID > 0 ?
                db::execSQL("
                               SELECT substring(val from '......(....)') || '/' || substring(val from '(.....).....') AS column
                                 FROM webset.std_progmod
                                WHERE stdrefid = " . $tsRefID . "
                                  AND typeofval = 'end'
                                  AND stsrefid = " . $RefID . "
                ")->getOne() :
                $student->getDate('stdcmpltdt')
    );

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');

    $edit->addSQLConstraint(
        'You are trying to add duplicate Placement', "
        SELECT 1
          FROM webset.std_placementcode
         WHERE stdrefid = " . $tsRefID . "
           AND pcrefid!=AF_REFID
    ");

    $edit->finishURL = CoreUtils::getURL('progmod_save.php', array('dskey' => $dskey));
    $edit->saveURL = CoreUtils::getURL('progmod_save.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('progmod.php', array('dskey' => $dskey));

    #Avoid Save and Add if record existing record
    if ($RefID > 0) $edit->saveAndAdd = false;

    $edit->printEdit();
?>