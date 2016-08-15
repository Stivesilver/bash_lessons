<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Extent of Participation';

	$edit->setSourceTable('webset.std_part_ext', 'pperefid');

	$edit->addGroup('General Information');
	$edit->addControl('Student Extent of Participation', 'select_radio')
		->sqlField('ppedrefid')
		->name('ppedrefid')
		->sql("
            SELECT ppedrefid,
                   CASE ppedearlychildhoodsw WHEN 'Y' THEN 'EC' WHEN 'N' THEN 'K12' END || ' - ' || ppedtext                   
              FROM webset.statedef_part_ext
             WHERE screfid = " . VNDState::factory()->id . "
               AND COALESCE(ppedearlychildhoodsw, 'N') = '" . $student->get('ecflag') . "'
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
             ORDER BY ppedseq, webset.statedef_part_ext.ppedtext
        ")
		->req();

	$textarea = $edit->addControl('Narrative', 'textarea')
		->sqlField('epdnarrtext')
		->name('epdnarrtext')
		->css('width', '100%')
		->css('height', '150px');

	if ($RefID == 0) {
		$textarea->tie('ppedrefid')
			->sql("
			SELECT REPLACE(ppeddfltnarrtext, 'StdName', '" . db::escape($ds->get('stdfirstname')) . "')
              FROM webset.statedef_part_ext
             WHERE ppedrefid = VALUE_01
		");
	}

	if (IDEACore::disParam(76) == 'Y') {
		$edit->addControl('Indicators', 'select_check')
			->sqlField('indicators')
			->sql("
                SELECT indrefid,
                       code || ' . ' || indicator
                  FROM webset.statedef_part_ext_indicator
                 WHERE ext_id = COALESCE(VALUE_01, 0)
                 ORDER BY code, indicator
            ")
			->tie('ppedrefid');
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_part_ext.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_part_ext.php', array('dskey' => $dskey));

	$edit->firstCellWidth = '30%';

	$edit->printEdit();

?> 