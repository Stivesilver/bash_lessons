<?php

    Security::init();
    FIFParts::init();

    $stdrefid = io::geti('stdrefid');
    $RefID = io::get('RefID');
    if ($RefID > 0 || $RefID == '0') {
        $edit = new EditClass('edit1', $RefID);
        $edit->title = 'Add/Edit 504 Processes';
        $edit->setSourceTable('webset.std_fif_history', 'hisrefid');

        $edit->addGroup("General Information");
        $edit->addControl("504 Ref ID", "protected")->sqlField('hisrefid')->sqlSavable(false);
        $edit->addControl("District Current 504 Process Status", "select")
            ->sqlField('difrefid')
            ->sql("
        	   SELECT difrefid,
	                  difdesc
	             FROM webset.disdef_fif_status district
	                  INNER JOIN webset.def_fif_status state ON state.fifrefid = district.statecode_id
	            WHERE vndrefid = VNDREFID
	              AND (state.enddate IS NULL OR NOW() < state.enddate)
	              AND (district.enddate IS NULL OR NOW() < district.enddate)
	            ORDER BY difdesc
        	")
            ->req();

        $edit->addControl("504 Initial Referral Date", "date")
            ->sqlField('initdate')
            ->name('initdate')
            ->req();

        $edit->addControl("504 Exit Date", "date")
            ->sqlField('exitdate')
            ->name('exitdate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($stdrefid)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('std_history.php', array('stdrefid' => $stdrefid));
        $edit->cancelURL = CoreUtils::getURL('std_history.php', array('stdrefid' => $stdrefid));
        $edit->firstCellWidth = "25%";

        $edit->addSQLConstraint('New period overlaps previously added 504 Process', "
            SELECT 1
              FROM webset.std_fif_history
             WHERE stdrefid = " . $stdrefid . "
               AND (COALESCE(initdate, '1000-01-01'::date), COALESCE(exitdate, '3000-01-01'::date))
                        OVERLAPS
                   (CASE WHEN '[initdate]' = '' THEN '1000-01-01' ELSE '[initdate]' END::date,
                    CASE WHEN '[exitdate]'  = '' THEN '3000-01-01' ELSE '[exitdate]'  END::date)
               AND hisrefid != AF_REFID
        ");

        $edit->addSQLConstraint('Exit Date should be greater than Initial Referral Date', "
            SELECT 1 WHERE '[initdate]' >= '[exitdate]' AND '[initdate]' != '' AND '[exitdate]' != ''
        ");

        $edit->saveAndEdit = true;
        $edit->saveAndAdd = false;

        $edit->printEdit();

        if ($RefID > 0) {
            $tabs = new UITabs('tabs');
            $tabs->addTab('Attached Documentation')
                ->url(CoreUtils::getURL('form_list.php', array('hisrefid' => $RefID)))
                ->name('forms');
            print $tabs->toHTML();
        }
    } else {
        $list = new ListClass();
        $list->title = '504 Processes Tracking';
        $list->multipleEdit = false;

        $list->SQL = "
            SELECT hisrefid,
                   hisrefid,
                   difdesc,
                   fifdesc,
                   initdate,
                   exitdate,
                   (SELECT count(1)
                      FROM webset.std_fif_forms s
                     WHERE s.hisrefid = fif.hisrefid),
                   CASE WHEN " . FIFParts::get('fifActivePlain') . " THEN 'Y' ELSE 'N' END as fifstatus
              FROM webset.std_fif_history fif
                   INNER JOIN webset.disdef_fif_status status ON fif.difrefid = status.difrefid
                   INNER JOIN webset.def_fif_status state ON status.statecode_id = state.fifrefid
             WHERE stdrefid = " . $stdrefid . "
             ORDER BY initdate desc, hisrefid
        ";

        $list->addColumn("504 Ref ID");
        $list->addColumn("District Current 504 Process Status")->sqlField('difdesc');
        $list->addColumn("System 504 Process Status")->sqlField('fifdesc');
        $list->addColumn("504 Initial Referral Date")->sqlField('initdate')->type('date');
        $list->addColumn("504 Exit Date")->sqlField('exitdate')->type('date');
        $list->addColumn('Attached Forms')
            ->type('tablehint')
            ->param("
                SELECT COALESCE(uploaded_title, fname), s.lastuser, TO_CHAR(s.lastupdate, 'MM-DD-YYYY')
                  FROM webset.std_fif_forms s
                       LEFT OUTER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
                 WHERE hisrefid = AF_REFID
                 ORDER BY s.lastupdate DESC, sfrefid
            ")
            ->dataCallback('markForms');
        $list->addColumn('504 Active')->hint('504 Status')->type('switch')->sqlField('fifstatus');


        $list->addURL = CoreUtils::getURL('std_history.php', array('stdrefid' => $stdrefid));
        $list->editURL = CoreUtils::getURL('std_history.php', array('stdrefid' => $stdrefid));

	    $list->addButton(
		    FFIDEAExportButton::factory()
			    ->setTable('webset.std_fif_history')
			    ->setKeyField('hisrefid')
		        ->setNesting('webset.std_fif_forms', 'sfrefid', 'hisrefid', 'webset.std_fif_history', 'hisrefid')
			    ->applyListClassMode()
	    );
	    
        $list->addRecordsProcess('Delete')
            ->message('Do you really want to delete this Sp Ed Enrollment?')
            ->url(CoreUtils::getURL('std_history_del.ajax.php'))
            ->type(ListClassProcess::DATA_UPDATE)
            ->progressBar(false);

        $list->printList();
    }

    function markForms($data, $col) {
        return UILayout::factory()
                ->addHTML($data[$col] . ' forms', '[color:blue; text-decoration:underline;]')
                ->toHTML();
    }

?>
