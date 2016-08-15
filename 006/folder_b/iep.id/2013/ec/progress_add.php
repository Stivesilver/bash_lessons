<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$siymrefid = io::geti('siymrefid');
	$sprrefid = io::geti('sprrefid');

	$edit = new EditClass('edit1', $sprrefid);

	$edit->title = 'Add/Edit EC IEP Goals Progress';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('Extent of Progress toward the goal', 'select_radio')
		->sqlField('int03')
		->sql("
            SELECT eprefid, 
                   epsdesc || ' - ' || epldesc
              FROM webset.disdef_progressrepext
             WHERE vndrefid = VNDREFID
             ORDER BY epseq, eprefid
        ")
		->breakRow()
		->req();

	$edit->addControl('Narrative', 'textarea')
		->sqlField('txt01')
		->css('width', '100%')
		->css('height', '50')
		->autoHeight(true);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value(IDEAAppArea::ID_EC_PROGRESS)->sqlField('area_id');

	if ($sprrefid == 0) {
		$edit->addControl('Period ID', 'hidden')->value(io::geti('period'))->sqlField('int05');
		$edit->addControl('Goal ID', 'hidden')->value(io::geti('grefid'))->sqlField('int01');
		$edit->addControl('Benchmark ID', 'hidden')->value(io::get('orefid') == 0 ? null : io::get('orefid'))->sqlField('int02');
		$edit->addControl('School Year ID', 'hidden')->value(io::geti('dsyrefid'))->sqlField('int04');
	}

	$edit->finishURL = CoreUtils::getURL('progress_list.php', array('dskey' => $dskey, 'siymrefid' => $siymrefid));
	$edit->cancelURL = CoreUtils::getURL('progress_list.php', array('dskey' => $dskey, 'siymrefid' => $siymrefid));

	$edit->saveAndAdd = false;
	$edit->firstCellWidth = '30%';

	$edit->printEdit();


?>
