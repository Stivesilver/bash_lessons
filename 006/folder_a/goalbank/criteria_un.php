<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_criteriaunits', 'dcurefid');

        $edit->title = 'Add/Edit Criteria Unit';

        $edit->addGroup('General Information');
        $edit->addControl('Criteria Unit', 'edit')->sqlField('dcudesc')->size(70);
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->finishURL = 'criteria_un.php';
        $edit->cancelURL = 'criteria_un.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Criteria Unit';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT dcurefid,
			       dcudesc,
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_criteriaunits
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH 
		     ORDER BY dcudesc
		";

        $list->addSearchField('Criteria Unit', "lower(dcudesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Criteria Unit', '80%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'criteria_un.php';
        $list->editURL = 'criteria_un.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_criteriaunits')
                ->setKeyField('dcurefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>