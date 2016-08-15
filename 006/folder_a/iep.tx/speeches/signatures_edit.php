<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
			INSERT INTO webset_tx.std_speech_recommend (stdrefid, iepyear)
            SELECT $tsRefID, $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_recommend
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear
                               )
           ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_recommend
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_recommend', 'refid');

	$edit->title = "Signatures";

	$edit->addGroup("General Information");
	$edit->addControl("Speech-Language Pathologist", "edit")
		->sqlField('signame')
		->size(60);

	$edit->addControl("Certification/License", "list")
		->sqlField('sigelator')
		->sql("
			SELECT validvalue, validvalue
          	  FROM webset.glb_validvalues
             WHERE valuename = 'TX_FIE_Titles'
          	 ORDER BY sequence_number
        ");

	$edit->addGroup("Second Line");
	$edit->addControl("Signature", "edit")
		->sqlField('signature')
		->size(60);

	$edit->addControl("Position", "edit")
		->sqlField('position')
		->size(60);

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "40%";

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL  = 'javascript:parent.parent.selectNext()';
	$edit->saveAndAdd = false;

	$edit->printEdit();

?>