<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $std_title = IDEAStudentCaption::get($tsRefID);

    #Refresh Data Storage
    $student = new IDEAStudent($tsRefID);
    $ds->set('stdIEPYear', $student->get('stdiepyear'));

    $list = new ListClass();

    $list->title = 'Select Student Folder';

    $list->SQL = "
        SELECT siymrefid,
			   ieptitle,
               TO_CHAR(siymiepbegdate, 'mm-dd-yyyy') as begdate,
               TO_CHAR(siymiependdate, 'mm-dd-yyyy') as enddate,
               CASE siymcurrentiepyearsw WHEN 'Y' THEN 'Yes' ELSE 'No' END,
               lastuser,
               TO_CHAR(lastupdate, 'mm-dd-yyyy HH:MIam') as lastupdate,
               siymcurrentiepyearsw
	   	  FROM webset.std_iep_year
		 WHERE stdrefid = " . $tsRefID . "
         ORDER BY siymiepbegdate desc
    ";

	$list->addColumn('Title')->dataCallback('markCurrentYear');
    $list->addColumn('Anticipated IEP Initiation Date')->dataCallback('markCurrentYear');
    $list->addColumn('Anticipated IEP Annual Review Date')->dataCallback('markCurrentYear');
    $list->addColumn('Current Student Folder')->dataCallback('markCurrentYear');
    $list->addColumn('Last User')->dataCallback('markCurrentYear');
    $list->addColumn('Last Update')->dataCallback('markCurrentYear');

    $list->editURL = CoreUtils::getURL('iep_cur_add.php', array('dskey' => $dskey));
    $list->hideCheckBoxes = true;
    $list->multipleEdit = false;

    $list->printList();

    function markCurrentYear($data, $col) {
        if ($data['siymcurrentiepyearsw'] == 'Y') {
            return UILayout::factory()
                    ->addHTML($data[$col], '[color:blue; font-weight: bold;]')
                    ->toHTML();
        } else {
            return $data[$col];
        }
    }

    io::js('api.window.changeTitle(' . json_encode($std_title) . ')');
?>