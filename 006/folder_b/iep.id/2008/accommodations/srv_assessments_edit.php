<?php

    Security::init();

    $dskey      = io::get('dskey');
    $RefID      = io::get('RefID');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $edit = new EditClass('edit1', $RefID);

    $edit->setSourceTable('webset.std_assess_state', 'sswarefid');

    $edit->title = "Add/Edit Testing Accommodations";

    $edit->addGroup("General Information");
    $edit->addControl("Subject/Assessment:", "list")
        ->sqlField('swarefid')
        ->sql("
            SELECT lrefid, aaadesc || ' - ' || swadesc
                   FROM webset.statedef_assess_links
                   INNER JOIN webset.statedef_assess_state ass ON ass.swarefid = assessment_id
                   INNER JOIN webset.statedef_assess_acc   sbj ON sbj.aaarefid = subject_id
             WHERE ass.screfid = 14
             ORDER BY 2
        ");

    $edit->addControl("Specify Assessment if other:", "edit")
         ->sqlField('sswanarr')
         ->size(60);

    $edit->addControl("Assessment Mode:", "select_check")
        ->sqlField('assessmode')
        ->sql("
            SELECT refid, validvalueid || ' - ' || validvalue
              FROM webset.glb_validvalues
             WHERE valuename = 'ID_Assessmode'
               AND (glb_enddate IS NULL or now()< glb_enddate)
             ORDER BY sequence_number
        ");

    $edit->addControl("Accommodations", "edit")
        ->sqlField('accomm_ids')
        ->css("display", "none");

	$edit->addControl("Specify Accommodation if Other", "textarea")
		->sqlField('accomod')
		->css("WIDTH", "100%")
		->css("HEIGHT", "50px")
		->autoHeight(true);

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")
         ->value(SystemCore::$userUID)
         ->sqlField('lastuser');

    $edit->addControl("Last Update", "protected")
         ->value(date("m-d-Y H:i:s"))
         ->sqlField('lastupdate');

    $edit->addControl("", "hidden")
         ->value($tsRefID)
         ->sqlField('stdrefid');

    $edit->addControl("", "hidden")
         ->value($stdIEPYear)
         ->sqlField('iepyear');

    $edit->printEdit();

?>
