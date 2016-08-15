<?php

    Security::init();

    $dskey         = io::get('dskey');
    $RefID         = io::geti('RefID');
    $ds            = DataStorage::factory($dskey);
    $tsRefID       = $ds->safeGet('tsRefID');
    $student       = new IDEAStudent($tsRefID);
    $edit          = new EditClass('edit1', $RefID);
	$accommodation = new IDEAAccommodation();
	$accIDs        = null;

    $edit->title = 'Add/Edit Program Modifications and Accommodations';
    $edit->firstCellWidth = '30%';

	# if use pop-up for add modification - generate select items
	if (io::get('accommodationID')) {
		$accIDs = io::get('accommodationID');
		#close pop-up after click on button 'save' or 'close'
		io::js('
        EditClass.get()
            .addEventListener(
                ObjectEvent.COMPLETE,
                function() {
                    api.window.destroy();
                }
            )


        ');
	}

	if ($RefID > 0) {
        $edit->setSourceTable('webset.std_srv_progmod', 'ssmrefid');
	}

    $edit->addGroup('General Information');

	if ($RefID > 0) {
	    $edit->addControl('Modification/Accommodation', 'select')
	        ->sqlField('stsrefid')
	        ->sql("
	            SELECT stsrefid, macdesc || ': ' || stsdesc
	              FROM webset.statedef_mod_acc acc
	                   INNER JOIN webset.statedef_mod_acc_cat cat ON acc.macrefid = cat.macrefid
	             WHERE acc.screfid = " . VNDState::factory()->id . "
	               AND (acc.recdeactivationdt IS NULL OR NOW()< acc.recdeactivationdt)
	               AND modaccommodationsw = 'Y'
	             ORDER BY 2
	        ")
	        ->req();
	} else {
		$edit->addControl(
			FFMultiSelect::factory('Modification/Accommodation')
				->sql("
					SELECT stsrefid, macdesc || ': ' || stsdesc
		              FROM webset.statedef_mod_acc acc
		                   INNER JOIN webset.statedef_mod_acc_cat cat ON acc.macrefid = cat.macrefid
		             WHERE acc.screfid = " . VNDState::factory()->id . "
		               AND (acc.recdeactivationdt IS NULL OR NOW()< acc.recdeactivationdt)
		               AND modaccommodationsw = 'Y'
		             ORDER BY 2
	             ")
				->value($accIDs)
				->name('stsrefid')
		)
		->sqlField('stsrefid')
		->req();
	}

    $edit->addControl('Beginning Date', 'date')
        ->sqlField('ssmbegdate')
	    ->name('ssmbegdate')
        ->value($student->getDate('stdenrolldt'));

    $edit->addControl('Ending Date', 'date')
        ->name('ssmenddate')
        ->sqlField('ssmenddate')
        ->value($student->getDate('stdcmpltdt'));

    $edit->addControl('Frequency', 'select')
        ->sqlField('ssmfreq')
        ->name('ssmfreq')
        ->sql("
            SELECT sfrefid, sfdesc
			  FROM webset.def_modfreq
             WHERE (enddate IS NULL or now()< enddate)
			 ORDER BY sfdesc
        ")
        ->value(
            db::execSQL("
                SELECT ssmfreq
                  FROM webset.std_srv_progmod
                 WHERE stdrefid = " . $tsRefID . "
                 ORDER BY ssmrefid DESC
                 LIMIT 1
        ")->getOne());

    $edit->addControl('Implementor', 'select')
        ->name('umrefid')
        ->sqlField('umrefid')
        ->sql("
            SELECT NULL, NULL
             UNION ALL
           (SELECT umrefid,  umlastname || ', ' || umfirstname
              FROM sys_usermst
             WHERE vndrefid = VNDREFID
               AND COALESCE(um_internal, true)
             ORDER BY 2)
            ")
        ->value(
            db::execSQL("
                SELECT umrefid
                  FROM webset.std_srv_progmod
                 WHERE stdrefid = " . $tsRefID . "
                ORDER BY ssmrefid DESC
                LIMIT 1
        ")->getOne()
    );

	$edit->addControl('Location', 'select')
		->name('malrefid')
		->sqlField('malrefid')
		->sql("
            SELECT malrefid, maldesc
              FROM webset.statedef_mod_acc_loc
             WHERE screfid = " . VNDState::factory()->id . "
             ORDER BY maldesc
        ")->value(
			db::execSQL("
                SELECT malrefid
                  FROM webset.std_srv_progmod
                 WHERE stdrefid = " . $tsRefID . "
                 ORDER BY ssmrefid DESC
                 LIMIT 1
        ")->getOne());

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')
	    ->value(SystemCore::$userUID)
	    ->sqlField('lastuser');

    $edit->addControl('Last Update', 'protected')
	    ->value(date('m-d-Y H:i:s'))
	    ->sqlField('lastupdate');

    $edit->addControl('Student ID', 'hidden')
	    ->value($tsRefID)->sqlField('stdrefid')
	    ->name('stdrefid');

    $edit->finishURL = CoreUtils::getURL('srv_progmod.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('srv_progmod.php', array('dskey' => $dskey));

	if ($RefID == 0) {
		$edit->setPresaveCallback('updateProgmod', 'srv_progmod_save.inc.php');
	}

    $edit->printEdit();
?>
