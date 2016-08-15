<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit Sp Ed Withdrawal Code';

        $edit->setSourceTable('webset.disdef_exit_codes', 'dexrefid');


        $edit->addGroup('General Information');
        $edit->addControl('State Withdrawal Code', 'list')
            ->sqlField('statecode_id')
            ->sql("
                SELECT NULL, NULL, 1
                 UNION
                SELECT secrefid, seccode || ' - ' || secdesc, 2
                  FROM webset.statedef_exitcategories
                 WHERE (screfid = " . VNDState::factory()->id . ")
                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                 ORDER BY 3, 2
            ")
            ->req();

        $edit->addControl('Code')->sqlField('dexcode')->name('dexcode')->size(5)->req();
        $edit->addControl('Code Description')->sqlField('dexdesc')->name('dexdesc')->size(50)->req();
        $edit->addControl('Display Order', 'integer')->sqlField('seqnum')->size(5);
        $edit->addControl('Expire Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint(
            'Sp Ed Withdrawal with such code or description already exists', "
            SELECT 1 
              FROM webset.disdef_exit_codes
             WHERE vndrefid = VNDREFID
               AND (dexcode = '[dexcode]' OR dexdesc = '[dexdesc]')
               AND dexrefid!=AF_REFID
        ");

        $edit->finishURL = 'exitcode.php';
        $edit->cancelURL = 'exitcode.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    } else {
        $list = new ListClass();
        $list->title = 'Sp Ed Withdrawal Codes';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT dexrefid,
                   dexcode,
                   dexdesc,
                   seccode || ' - ' || secdesc,
                   district.seqnum,
                   CASE WHEN NOW() > district.enddate  THEN 'N' ELSE 'Y' END  as status
              FROM webset.disdef_exit_codes district
                   LEFT OUTER JOIN webset.statedef_exitcategories state ON state.secrefid = district.statecode_id
             WHERE vndrefid = VNDREFID
               AND (state.recdeactivationdt IS NULL or now()<state.recdeactivationdt)
                   ADD_SEARCH
             ORDER BY seqnum, dexcode
        ";

        $list->addSearchField('Code', 'dexcode');
        $list->addSearchField('Code Description', "LOWER(dexdesc)  like '%' || LOWER('ADD_VALUE')|| '%'");
        $list->addSearchField(
            FFIDEAStatus::factory()
                ->sqlField("CASE WHEN NOW() > district.enddate THEN 'N' ELSE 'Y' END")
        );

        $list->addColumn('Code');
        $list->addColumn('Code Description');
        $list->addColumn('State Withdrawal Code');
        $list->addColumn('Display Order');
        $list->addColumn('Active')->type('switch');

        $list->addURL = 'exitcode.php';
        $list->editURL = 'exitcode.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_exit_codes')
                ->setKeyField('dexrefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
