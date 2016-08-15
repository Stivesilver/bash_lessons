<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_goalsentencepreface', 'gsfrefid');

        $edit->title = 'Add/Edit Sentence Preface';

        $edit->addGroup('General Information');
        $edit->addControl('Sentence Preface', 'edit')->sqlField('gsptext')->size(70)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->finishURL = 'st_preface.php';
        $edit->cancelURL = 'st_preface.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Sentence Preface';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT gsfrefid,
			       gsptext,
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_goalsentencepreface
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH 
		     ORDER BY gsptext
		";

        $list->addSearchField('Sentence Preface', "lower(gsptext)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Sentence Preface', '80%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'st_preface.php';
        $list->editURL = 'st_preface.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_goalsentencepreface')
                ->setKeyField('gsfrefid')
                ->applyListClassMode()
        );

        $list->printList();
    }
?>
