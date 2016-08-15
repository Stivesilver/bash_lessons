<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.es_std_evalproc', 'eprefid');

	$edit->title = "Student Evaluation Process Tracking";

	$edit->addGroup('General Information');

	$edit->addControl("Date Signed Consent Received", "date")->sqlField('date_start');
	$edit->addControl(FFSelect::factory("Evaluation Report Type"))
		->sql("
			SELECT essrtrefid ,essrtdescription
              FROM webset.es_statedef_reporttype
              WHERE screfid = " . VNDState::factory()->id . "
                AND essrtdescription !='Review of Existing Data'
                AND essrtdescription !='Evaluation Plan'
              ORDER BY seq_ord, essrtdescription DESC
		")
		->sqlField('ev_type');

	$edit->addControl(FFSelect::factory("Reason for Referral"))
		->sql("
			SELECT rrefid ,rdesc
              FROM webset.es_disdef_ref_reason
              WHERE NOW() >= COALESCE(recactivationdt, TO_DATE('01-01-1000', 'dd-mm-yyyy')) AND NOW() < COALESCE(recdeactivationdt, TO_DATE('01-01-4000', 'dd-mm-yyyy'))
                AND vndrefid = VNDREFID
              ORDER BY rseq, rdesc DESC
		")
		->sqlField('reason_for_referral');

	$edit->addControl(FFSelect::factory("Reason for Evaluation"))
		->sql("
			SELECT rrefid ,rdesc
              FROM webset.es_disdef_eval_reason
              WHERE NOW() >= COALESCE(recactivationdt, TO_DATE('01-01-1000', 'dd-mm-yyyy')) AND NOW() < COALESCE(recdeactivationdt, TO_DATE('01-01-4000', 'dd-mm-yyyy'))
              AND vndrefid = VNDREFID
              ORDER BY rseq, rdesc  desc
		")
		->sqlField('reason_for_eval');

	$edit->addControl("Specify")
		->sqlField('reason_for_eval_oth');

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")
		->sqlField('stdrefid')
		->value($tsRefID);

	if (io::geti('RefID') == 0) {
		$edit->setPostsaveCallback('postSave', './current_save.inc.php', array('dskey' => $dskey));
	}

	$edit->finishURL = "javascript:parent.api.reload({'eval_set': 'add'});";
	$edit->cancelURL = CoreUtils::getURL('./process_list.php', array('dskey' => $dskey));
	$edit->saveAndAdd = false;

	$edit->firstCellWidth = "30%";

	$edit->printEdit();
?>
