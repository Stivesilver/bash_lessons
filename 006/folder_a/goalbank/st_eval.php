<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_ksaeval', 'erefid');

        $edit->title = 'Add/Edit Sentence Evaluation';

        $edit->addGroup('General Information');
        $edit->addControl('Evaluation', 'edit')->sqlField('edesc')->size(70)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->finishURL = 'st_eval.php';
        $edit->cancelURL = 'st_eval.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Sentence Evaluation';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT erefid,
			       edesc,
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_ksaeval
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH 
		     ORDER BY edesc
		";

        $list->addSearchField('Evaluation', "lower(edesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Evaluation', '80%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'st_eval.php';
        $list->editURL = 'st_eval.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_ksaeval')
                ->setKeyField('erefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
