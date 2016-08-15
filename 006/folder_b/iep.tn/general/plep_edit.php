<?php

	Security::init();

	$RefID = io::get('RefID');

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit', $RefID);

	$edit->title = 'Add/Edit Present Levels of Development';

	$edit->setSourceTable('webset.std_in_pglp', 'pglprefid');

	$edit->addGroup('General Information');

	$edit->addControl('Area', 'select')
		->sqlField('tsnrefid')
		->name('tsnrefid')
		->sql("
			SELECT tsn.tsnrefid,
			       tsn.tsndesc
		   	  FROM webset.disdef_tsn AS tsn
	         WHERE tsn.vndrefid = VNDREFID
	           AND (tsn.recdeactivationdt IS NULL OR NOW()< tsn.recdeactivationdt)
	           " . ($RefID > 0 ? "" : "
	                AND NOT EXISTS(
	                    SELECT 1
                          FROM webset.std_in_pglp AS pglp
                         WHERE pglp.stdrefid = " . $tsRefID . "
                           AND pglp.iepyear = " . $stdIEPYear . "
                           AND pglp.tsnrefid = tsn.tsnrefid
                    )") . "
	        ORDER BY tsnnum
		")
		->req();

	$edit->addControl('Strengths', 'textarea')
		->sqlField('strengths')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addControl('Concerns/Needs', 'textarea')
		->sqlField('concerns')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addControl('By')
		->sqlField('pglpnarrative')
		->css('width', '100%');

	$edit->addControl('Instrument')
		->sqlField('impact')
		->css('width', '100%');

	$edit->addControl('Date', 'date')
		->sqlField('pgdate');

	$edit->addControl('Chron. Age', 'integer')
		->sqlField('glrefid')
		->css('width', '100px');

	$edit->addControl('Adj. Age')
		->sqlField('pglplgrade')
		->css('width', '100px');

	$edit->addUpdateInformation();

	$edit->addControl('Student ID', 'hidden')
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl('IEP Year ID', 'hidden')
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>