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

    $list->title = 'New IEP Year Process';

    $list->SQL = "
        SELECT siymrefid,
               TO_CHAR(siymiepbegdate, 'mm-dd-yyyy'),
               TO_CHAR(siymiependdate, 'mm-dd-yyyy'),
               CASE siymcurrentiepyearsw WHEN 'Y' THEN 'Yes' ELSE 'No' END,
               lastuser,
               TO_CHAR(lastupdate, 'mm-dd-yyyy HH:MIam') as lastupdate,
               siymcurrentiepyearsw
          FROM webset.std_iep_year
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY siymiepbegdate desc
    ";

    $list->addColumn('Anticipated IEP Initiation Date')->dataCallback('markCurrentYear');
    $list->addColumn('Anticipated IEP Annual Review Date')->dataCallback('markCurrentYear');
    $list->addColumn('Current IEP Year')->dataCallback('markCurrentYear');
    $list->addColumn('Last User')->dataCallback('markCurrentYear');
    $list->addColumn('Last Update')->dataCallback('markCurrentYear');

    $list->addURL = CoreUtils::getURL('iep_proc_add.php', array('dskey' => $dskey));

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_iep_year')
            ->setKeyField('siymrefid')
            ->applyListClassMode()
    );

	$list->editURL = CoreUtils::getURL('iep_proc_add.php', array('dskey' => $dskey));
	
    if (SystemCore::$AccessType == "1") {

        $list->addRecordsProcess('Delete')
            ->message('Do you really want to delete this IEP Year?')
            ->url(CoreUtils::getURL('iep_proc_delete.ajax.php', array('dskey' => $dskey)))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);
    }

    $list->getButton(ListClassButton::ADD_NEW)->value('Create New IEP Year');

    if ($student->get('stdiepyear') > 0) {
        if (IDEACore::disParam(49) == 'Y') {
            $allowCreate = include(CoreUtils::getPhysicalPath('/apps/idea/sys_maint/sped_menu/includes/ks_builder.php'));
            if (!$allowCreate['condition']) {
                $list->getButton(ListClassButton::ADD_NEW)
                    ->disabled()
                    ->help('Can not create New IEP Year until current IEP is archived.');
            }
        }
    }

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