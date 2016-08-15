<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $std_title = IDEAStudentCaption::get($tsRefID);
	$refresh = io::geti('refresh');

    #Refresh Data Storage
    $student = new IDEAStudent($tsRefID);
    $ds->set('stdIEPYear', $student->get('stdiepyear'));

    $list = new ListClass();

    $list->title = 'Select Evaluation Process';

    $list->SQL = "
		SELECT ep.eprefid,
		       TO_CHAR(ep.date_start, 'MM-DD-YYYY') AS sdata,
		       rp.essrtdescription,
		       ep.ep_current_sw,
		       ep.lastuser,
		       ep.lastupdate
		  FROM webset.es_std_evalproc AS ep
		       INNER JOIN webset.es_statedef_reporttype AS rp ON rp.essrtrefid = ep.ev_type
		 WHERE (1=1) ADD_SEARCH
		   AND stdrefid = " . $tsRefID . "
		 ORDER BY ep.date_start DESC
    ";

	$list->addColumn("Evaluation Start Date")->sqlField('sdata')->dataCallback('markCurrentEval');
	$list->addColumn("Evaluation Type")->sqlField('essrtdescription')->dataCallback('markCurrentEval');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('datetime');

    $list->editURL = CoreUtils::getURL('current_add.php', array('dskey' => $dskey));

    $list->hideCheckBoxes = true;
    $list->multipleEdit = false;

    $list->printList();

    function markCurrentEval($data, $col) {
        if ($data['ep_current_sw'] == 'Y') {
            return UILayout::factory()
                    ->addHTML($data[$col], '[color:blue; font-weight: bold;]')
                    ->toHTML();
        } else {
            return $data[$col];
        }
    }

	if($refresh) {
		io::js($ds->safeGet('refresh_screen_js'));
	}
    io::js('api.window.changeTitle(' . json_encode($std_title) . ')');
?>
