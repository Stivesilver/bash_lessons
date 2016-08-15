<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_fie_general (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
                 WHERE NOT EXISTS (
                 	SELECT 1 FROM webset_tx.std_fie_general
                    WHERE stdrefid = $tsRefID
                      AND iepyear = $stdIEPYear
                    )
        ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT vrefid
		  FROM webset_tx.std_fie_general
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new editClass('edit1', $RefID);

	$edit->title          = 'Copy';
	$edit->firstCellWidth = "30%";
	$edit->saveAndEdit    = true;
	$edit->saveAndAdd     = false;

	$edit->setSourceTable('webset_tx.std_fie_general', 'vrefid');

	$edit->addControl("A copy was provided to the parent", "SELECT_RADIO")
		->sqlField('assur_copy')
		->data(
			array(
				'Y' => 'Yes',
				'N' => 'No'
			)
		);

	$edit->addControl("Date provided", "DATE")
		->sqlField('assur_copy_dt');

	$edit->finishURL  = 'javascript:parent.parent.selectNext()';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>