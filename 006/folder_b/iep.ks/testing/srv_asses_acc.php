<?php

    Security::init();

    $dskey         = io::get('dskey');
    $RefID         = io::geti('RefID');
    $ds            = DataStorage::factory($dskey);
    $tsRefID       = $ds->safeGet('tsRefID');
    $RefID         = io::get('RefID');
	$accommodation = new IDEAAccommodation();

    if ($RefID == '') {
        $list          = new ListClass();
	    $accommodation = new IDEAAccommodation();
	    # if exist accommodations without modifications - add alert
	    $accommodation->setAttr('tsRefID', $tsRefID);
	    $accommodation->getAccommodations();
	    $accommodation->sumAccomodations();
	    $accommodation->getModifications();
	    $accommodation->sumModifications();
	    $accommodation->checkRelationsAcc();

	    if ($accommodation->getAttr('countNotBinding') > 0) {
		    $message = $accommodation->buildNotBindingMessage('ids_assessments');
		    $list->addObject(
			    UIMessage::factory('', UIMessage::NOTE)
				    ->message($message)
				    ->textAlign('left')
				    ->width('100%'),
			    ListClassElement::TITLE_BAR_UNDER
		    );
	    }

        $list->title = 'Assessment Accommodations';
        $list->SQL   = "
            SELECT std.saarefid,
                   REPLACE('<b>' || COALESCE(aacdesc, '') || ':</b> ' || macc.stsdesc, '<b>:</b>', ''),
                   sacc.aaadesc
              FROM webset.std_assess_acc std
                   INNER JOIN webset.statedef_assess_acc sacc ON std.aaarefid = sacc.aaarefid
                   INNER JOIN webset.statedef_mod_acc macc ON macc.stsrefid = std.marefid
                   LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
             WHERE std.stdrefid = " . $tsRefID . "
             ORDER BY stsseq, stsdesc
        ";

        $list->addColumn("Accommodation");
        $list->addColumn("Areas of Assessment");

        $list->deleteTableName = "webset.std_assess_acc";
        $list->deleteKeyField  = "saarefid";

        $list->addURL  = CoreUtils::getURL('srv_asses_acc.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_asses_acc.php', array('dskey' => $dskey));

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

	    io::jsVar('dskey', $dskey);

    } else {
        $edit = new EditClass("edit1", $RefID);
	    if (io::get('accommodationID')) {
	        $accommodation->setAttr('accommodations', explode(',', io::get('accommodationID')));
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

	    $accommodation->sumAccomodations();

        $edit->title = 'Assessment Accommodations';

	    if ($RefID > 0) {
            $edit->setSourceTable('webset.std_assess_acc', 'saarefid');
	    } else {
		    $edit->setPresaveCallback('addAsses', 'srv_asses_save.inc.php');
	    }

        $edit->addGroup('General Information');

	    if ($RefID > 0) {
	        $edit->addControl('Accommodation', 'select')
	            ->name('marefid')
	            ->sqlField('marefid')
	            ->sql("
	              SELECT stsrefid,
	                     TRIM(COALESCE(aacdesc, '')||': '||stsdesc, ': ')
	                FROM webset.statedef_mod_acc macc
	                     LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
	               WHERE macc.screfid = " . VNDState::factory()->id . "
	                 AND UPPER(assessmentsw) = 'Y'
	                 AND (macc.recdeactivationdt IS NULL OR NOW() < macc.recdeactivationdt)
	               ORDER BY stsseq, stscode, stsdesc
	            ")
	            ->emptyOption(true)
	            ->req();
	    } else {
		    $accommodation->setControlByAcc($edit);
		    /*
		    $edit->addControl(
			    FFMultiSelect::factory('Accommodation')
				    ->sql("
						SELECT stsrefid,
			                   TRIM(COALESCE(aacdesc, '')||': '||stsdesc, ': ')
			              FROM webset.statedef_mod_acc macc
			                   LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
			             WHERE macc.screfid = " . VNDState::factory()->id . "
			               AND UPPER(assessmentsw) = 'Y'
			               AND (macc.recdeactivationdt IS NULL OR NOW() < macc.recdeactivationdt)
			             ORDER BY stsseq, stscode, stsdesc
	             ")
			    ->name('marefid')
		        )
			    ->req();
			    */
	    }

        $edit->addControl('Details', 'textarea')
            ->name('saashortdesc')
            ->sqlField('saashortdesc');

        $edit->addControl('Areas of Assessment', 'select')
            ->name('aaarefid')
            ->sqlField('aaarefid')
            ->sql("
              SELECT aaarefid, 
                     aaadesc
                FROM webset.statedef_assess_acc
               WHERE screfid = " . VNDState::factory()->id . "
                 AND (recdeactivationdt IS NULL OR NOW() < recdeactivationdt)
               ORDER BY aaadesc
            ")
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid')->name('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_asses_acc.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_asses_acc.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?>

<script type="text/javascript">

	function addAccommodation(accommodationID) {
		url = api.url('srv_asses_acc.php',
			{'dskey': dskey, 'RefID': 0, 'accommodationID': accommodationID});
		win = api.window.open(' ', url)
			.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reloadPage();
			}
		);
	}

</script>