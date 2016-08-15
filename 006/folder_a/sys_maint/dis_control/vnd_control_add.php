<?php
	Security::init();

	$RefID = io::geti('RefID');

	$SQL = "
        SELECT dckey,
               dcdesc,
               dcsql
          FROM webset.def_discontrol
         WHERE dcrefid = " . $RefID . "
    ";
	$result = db::execSQL($SQL);

	$dckey = $result->fields['dckey'];
	$dcdesc = db::escape($result->fields['dcdesc']);
	$dcsql = $result->fields['dcsql'];

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit District Control Option';

	$edit->SQL = "
        SELECT '" . $dcdesc . "' as dcdesc,
               dis.paramvalue,
               dis.lastuser,
               dis.lastupdate,
               dis.defrefid
          FROM webset.def_discontrol gen
               LEFT OUTER JOIN webset.disdef_control dis ON gen.dcrefid = dis.defrefid AND vndrefid = VNDREFID
         WHERE gen.dcrefid = $RefID
    ";

	if ($dckey == 'SQL' OR $dckey == 'SQL_CHECK') {
		$dcsql = str_replace('AF_STATEREFID', VNDState::factory()->id, $dcsql);
		$dcsql = str_replace('AF_VNDREFID', SystemCore::$VndRefID, $dcsql);
	} else {
		$dcsql = "
            SELECT validvalueid,
                   validvalue
              FROM webset.glb_validvalues
             WHERE valuename = '" . $dckey . "'
        ";
	}

	$edit->finishURL = CoreUtils::getURL('./vnd_control_save.php', array('category' => io::get('category')));
	$edit->saveURL = CoreUtils::getURL('./vnd_control_save.php', array('category' => io::get('category')));
	$edit->cancelURL = CoreUtils::getURL('./vnd_control.php', array('category' => io::get('category')));

	$edit->addGroup('General Information');
	$edit->addControl('District Control Option', 'protected')->sqlField('dcdesc');

	if ($dckey == 'TEXT') {
		$edit->addControl('Value')
			->sqlField('paramvalue')
			->name('paramvalue')
			->size(50);
	} elseif ($dckey == 'TEXTAREA') {
		$edit->addControl('Value', 'textarea')
			->sqlField('paramvalue')
			->name('paramvalue')
			->css('WIDTH', '100%')
			->css('HEIGHT', '100px');
	} elseif ($dckey == 'SQL_CHECK') {
		$edit->addControl('Value', 'select_check')
			->sqlField('paramvalue')
			->name('paramvalue')
			->displaySelectAllButton(false)
			->breakRow(true)
			->sql($dcsql);
	} else {
		$edit->addControl('Value', 'select')
			->sqlField('paramvalue')
			->name('paramvalue')
			->sql($dcsql)
			->emptyOption(true);
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('defrefid', 'hidden')
		->value($RefID)
		->sqlField('defrefid')
		->name('defrefid');

	$edit->saveLocal = false;
	$edit->saveAndAdd = false;
	$edit->firstCellWidth = '30%';

	$edit->printEdit();

?>
