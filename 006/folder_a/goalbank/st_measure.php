<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_measure', 'mrefid');

        $edit->title = 'Add/Edit Sentence Measure';

        $edit->addGroup('General Information');
        $edit->addControl('Measure', 'edit')->sqlField('mdesc')->size(70)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->finishURL = 'st_measure.php';
        $edit->cancelURL = 'st_measure.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Sentence Measure';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT mrefid,
			       mdesc,
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_measure
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH 
		     ORDER BY mdesc
		";

        $list->addSearchField('Measure', "lower(mdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Measure', '80%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'st_measure.php';
        $list->editURL = 'st_measure.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_measure')
                ->setKeyField('mrefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
