<?php

    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $RefID      = io::geti('RefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $edit       = new EditClass("edit1", $RefID);

    $SQL        = "
        SELECT TO_CHAR(stdenrolldt, 'MM/DD/YYYY'),
               TO_CHAR(stdcmpltdt,'MM/DD/YYYY')
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = " . $RefID
        ;

    $result = db::execSQL($SQL);

    if (!$result) se($SQL);

    $bgdate = $result->fields[0];
    $endate = $result->fields[1];

    $edit->setSourceTable('webset.std_srv_progmod', 'ssmrefid');

    $edit->saveLocal     = true;
    $edit->recordLocking = false;
    $edit->title         = "Add/Edit Classroom Accommodations";

    $edit->addGroup("General Information");
    $edit->addControl("Location", "edit")
         ->name('bcpdesc')
         ->sqlField('bcpdesc');

    $edit->addControl("Beginning Date", "date")
         ->name('ssmbegdate')
         ->sqlField('ssmbegdate');

    $edit->addControl("Anticipated Duration", "edit")
         ->name('ssmteacherother')
         ->sqlField('ssmteacherother');

    $edit->addControl("Frequency", "hidden")
         ->name('ssmfreq')
         ->sqlField('ssmfreq');

    $edit->addControl("Implementor", "hidden")
         ->sqlField('umrefid');

    $edit->addControl("Location", "hidden")
         ->sqlField('malrefid');

    $edit->addControl("Specify:", "hidden")
         ->sqlField('ssmbldother');

    $edit->addGroup("Modifications/Accommodations");

    $edit->addControl(
        FFMultiSelect::factory('Modifications')
            ->sql("
				SELECT stsrefid,
                       COALESCE(macdesc || ': ', '') || stsdesc,
                       acc.stsdesc
                  FROM webset.statedef_mod_acc acc
                       LEFT OUTER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = acc.macrefid
                 WHERE acc.screfid = " . VNDState::factory()->id . "
                   AND (acc.recdeactivationdt IS NULL or now()< acc.recdeactivationdt)
                   AND modaccommodationsw = 'Y'
                   AND (cat.enddate IS NULL or now()< cat.enddate)
                 ORDER BY seq_num, stsseq, stscode, stsdesc
             ")
            ->name('modifications')
    );

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")
         ->value(SystemCore::$userUID)
         ->sqlField('lastuser');

    $edit->addControl("Last Update", "protected")
         ->value(date("m-d-Y H:i:s"))
         ->sqlField('lastupdate');

    $edit->addControl("tsRefID", "hidden")
         ->name('tsRefID')
         ->value($tsRefID)
         ->sqlField('stdrefid');

    $edit->addControl('stdIEPYear', 'hidden')
         ->name('stdIEPYear')
         ->value($stdIEPYear);

    $edit->setPresaveCallback('updateProgmod', 'srv_progmod_save.inc.php');

    $edit->printEdit();

?>
