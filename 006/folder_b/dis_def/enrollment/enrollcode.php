<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit Sp Ed Enrollment Code';

        $edit->setSourceTable('webset.disdef_enroll_codes', 'denrefid');

        $edit->addGroup('General Information');
        $edit->addControl('State Enrollment Code', 'list')
            ->sqlField('statecode_id')
            ->sql("
                SELECT NULL, NULL, 1
                 UNION
                SELECT enrrefid, enrcode || ' - ' || enrdesc, 2
                  FROM webset.statedef_enroll_codes
                 WHERE (screfid = " . VNDState::factory()->id . ")
                   AND (enddate IS NULL or now()< enddate)
                 ORDER BY 3, 2
            ")
            ->req();

        $edit->addControl('Code')->sqlField('dencode')->name('dencode')->size(5)->req();
        $edit->addControl('Code Description')->sqlField('dendesc')->name('dendesc')->size(50)->req();
        $edit->addControl('Display Order', 'integer')->sqlField('seqnum')->size(5);
        $edit->addControl('Expire Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint(
            'Enrollment with such code or description already exists', "
            SELECT 1 
              FROM webset.disdef_enroll_codes
             WHERE vndrefid = VNDREFID
               AND (dencode = '[dencode]' OR dendesc = '[dendesc]')
               AND denrefid!=AF_REFID
        ");

        $edit->finishURL = 'enrollcode.php';
        $edit->cancelURL = 'enrollcode.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Sp Ed Enrollment Codes';
        $list->showSearchFields = true;

        $list->SQL = "
            SELECT denrefid,
                   dencode,
                   dendesc,
                   enrcode || ' - ' || enrdesc ,
                   district.seqnum,
                   CASE WHEN NOW() > district.enddate  THEN 'N' ELSE 'Y' END  as status,
                   sped_active
              FROM webset.disdef_enroll_codes district
                   INNER JOIN webset.statedef_enroll_codes state ON state.enrrefid = district.statecode_id
             WHERE vndrefid = VNDREFID
               AND (state.enddate IS NULL or now()<state.enddate)
                   ADD_SEARCH
             ORDER BY seqnum, dencode
        ";

        $list->addSearchField('Code', 'dencode');
        $list->addSearchField('Code Description', "LOWER(dendesc)  like '%' || LOWER('ADD_VALUE')|| '%'");
        $list->addSearchField(
            FFIDEAStatus::factory()
                ->sqlField("CASE WHEN NOW() > district.enddate THEN 'N' ELSE 'Y' END")
        );

        $list->addColumn('Code');
        $list->addColumn('Code Description');
        $list->addColumn('State Enrollment Code')->dataCallback('markActiveCode');
        $list->addColumn('Display Order');
        $list->addColumn('Active')->type('switch');

        $list->addURL = 'enrollcode.php';
        $list->editURL = 'enrollcode.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_enroll_codes')
                ->setKeyField('denrefid')
                ->applyListClassMode()
        );

        $list->printList();

        //Purge Cache file with active sped codes
        $filepath = SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_sped.php';

        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    function markActiveCode($data, $col) {
        if ($data['sped_active'] == 'Y') {
            return '<b>' . $data[$col] . '</b>';
        } else {
            return $data[$col];
        }
    }

?>
