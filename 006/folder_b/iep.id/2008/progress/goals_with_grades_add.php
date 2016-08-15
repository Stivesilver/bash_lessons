<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$siymrefid = io::geti('siymrefid');
	$sprrefid = io::geti('sprrefid');

	$edit = new EditClass('edit1', $sprrefid);

	$edit->title = 'Add/Edit IEP Goals Progress';

	$edit->setSourceTable('webset.std_oth_progress', 'sprrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Extent of Progress toward the goal', 'select_radio')
		->sqlField('eprefid')
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
		->sqlField('sprnarative')
		->css('width', '100%')
		->css('height', '50')
		->autoHeight(true);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	if ($sprrefid == 0) {
		$edit->addControl('Period ID', 'hidden')->value(io::geti('period'))->sqlField('sprmarkingprd');
		$edit->addControl('Goal ID', 'hidden')->value(io::geti('grefid'))->sqlField('stdgoalrefid');
		$edit->addControl('Benchmark ID', 'hidden')->value(io::get('orefid') == 0 ? null : io::get('orefid'))->sqlField('stdbenchmarkrefid');
		$edit->addControl('School Year ID', 'hidden')->value(io::geti('dsyrefid'))->sqlField('dsyrefid');
	}

	$edit->finishURL = CoreUtils::getURL('goals_with_grades.php', array('dskey' => $dskey, 'siymrefid' => $siymrefid));
	$edit->cancelURL = CoreUtils::getURL('goals_with_grades.php', array('dskey' => $dskey, 'siymrefid' => $siymrefid));

	$edit->saveAndAdd = false;
	$edit->firstCellWidth = '30%';

	$edit->printEdit();


?>
