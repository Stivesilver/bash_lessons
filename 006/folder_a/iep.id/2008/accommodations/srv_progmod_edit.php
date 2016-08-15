<?php

    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $RefID      = io::geti('RefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $edit       = new EditClass("edit1", $RefID);

    $edit->setSourceTable('webset.std_srv_progmod', 'ssmrefid');

    $edit->title = "Edit Accommodation";

    $edit->addGroup("General Information");
    $edit->addControl("Accommodation", "select")
         ->sqlField('stsrefid')
         ->sql(
            "SELECT stsrefid,
                    macdesc || ' ' || CASE WHEN length(stsdesc)>100 THEN substr(stsdesc,0,100) || '...' ELSE stsdesc END
               FROM webset.statedef_mod_acc acc
                    LEFT OUTER JOIN webset.statedef_mod_acc_cat cat  ON cat.macrefid = acc.macrefid
              WHERE acc.screfid = " . VNDState::factory()->id . "
                AND (acc.recdeactivationdt IS NULL or now()< acc.recdeactivationdt)
              ORDER BY macdesc, stsdesc
            ");

	$edit->addControl("Benchmark", "textarea")
		->sqlField('ssmmbrother')
		->css("WIDTH", "100%")
		->css("HEIGHT", "50px")
		->autoHeight(true);

    $edit->addControl("Location", "edit")
         ->sqlField('bcpdesc')
         ->size(50);

    $edit->addControl("Beginning Date", "date")
         ->sqlField('ssmbegdate');

    $edit->addControl("Anticipated Duration", "edit")
         ->sqlField('ssmteacherother')
         ->size(50);

    $edit->addGroup("Update Information",  true);
    $edit->addControl("Last User", "protected")
         ->value(SystemCore::$userUID)
         ->sqlField('lastuser');

    $edit->addControl("Last Update", "protected")
         ->value(date("m-d-Y H:i:s"))
         ->sqlField('lastupdate');

    $edit->addControl("tsRefID", "hidden")
         ->value($tsRefID)
         ->sqlField('stdrefid');

    $edit->addControl("stdrefid", "hidden")
         ->value($stdIEPYear)
         ->sqlField('iepyear');

    $edit->printEdit();

?>
