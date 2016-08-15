<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory(io::get('dskey'))->safeGet('tsRefID');

    $edit = new EditClass("edit1", io::geti('RefID'));

    $edit->title = 'Add/Edit Add/Edit Disability Category';

    $edit->setSourceTable('webset.std_disabilitymst', 'sdrefid');

    $edit->addControl('Disability', 'select_radio')
        ->sqlField('dcrefid')
        ->name('dcrefid')
        ->sql("
            SELECT dcrefid,
                   COALESCE (dccode || ' - ', '') || dcdesc
		      FROM webset.statedef_disablingcondition
		     WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
             ORDER BY dccode
        ")
        ->breakRow()
        ->req();

    $edit->addControl('Type', 'select_radio')
        ->sqlField('sdtype')
        ->sql("
           SELECT validvalueid, validvalue
             FROM webset.glb_validvalues
            WHERE valuename = 'IDDisabilityType'
              AND (glb_enddate IS NULL or now()< glb_enddate)
            ORDER BY validvalueid
        ")
        ->breakRow()
        ->req();

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->addSQLConstraint('You are trying to add duplicate Disability', "
        SELECT 1
	      FROM webset.std_disabilitymst
	     WHERE stdrefid = " . $tsRefID . "
	       AND dcrefid = [dcrefid]
	       AND sdrefid != AF_REFID
    ");

    $edit->finishURL = CoreUtils::getURL('cd_dis_cat.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('cd_dis_cat.php', array('dskey' => $dskey));

    $edit->printEdit();
?>