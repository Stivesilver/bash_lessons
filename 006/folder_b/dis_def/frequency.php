<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Frequency';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT sfrefid,
                   sfdesc,
                   seqnum,
                   CASE WHEN NOW() > enddate  THEN 'N' ELSE 'Y' END  as status
              FROM webset.disdef_frequency
             WHERE vndrefid = VNDREFID
                   ADD_SEARCH
            ORDER BY seqnum, sfdesc
        ";

        $list->addSearchField('Frequency', "LOWER(sfdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Frequency');
        $list->addColumn('Sequence');
        $list->addColumn('Active')->type('switch');

        $list->addURL = 'frequency.php';
        $list->editURL = 'frequency.php';

        if (substr(SystemCore::$userUID, 0, 8) != 'gsupport' && VNDState::factory()->id == "ID") {
            $list->addURL = "";
        }

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_frequency')
                ->setKeyField('sfrefid')
                ->applyListClassMode()
        );

        $list->printList();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Frequency';

        $edit->setSourceTable('webset.disdef_frequency', 'sfrefid');

        $edit->addGroup('General Information');
        $edit->addControl('Frequency', 'edit')->sqlField('sfdesc')->name('sfdesc')->size(90)->req();
        $edit->addControl('Sequence', 'integer')->sqlField('seqnum')->size(10);
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint('Such Frequency already exists', "
                SELECT 1
                  FROM webset.disdef_frequency
                 WHERE vndrefid = VNDREFID
                   AND sfdesc = '[sfdesc]'
                   AND sfrefid!=AF_REFID
        ");

        $edit->finishURL = 'frequency.php';
        $edit->cancelURL = 'frequency.php';

        $edit->firstCellWidth = '30%';

        $edit->printEdit();
    }
?>
