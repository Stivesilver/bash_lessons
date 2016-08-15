<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {

        $edit = new EditClass('edit1', $RefID);

        $edit->setSourceTable('webset.disdef_bgb_itembank_cat', 'ibcrefid');

        $edit->title = 'Add/Edit Items Bank Category';

        $edit->addGroup('General Information');
        $edit->addControl('Category', 'edit')->sqlField('ibcdesc')->size(70)->req();
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->finishURL = 'ib_cat.php';
        $edit->cancelURL = 'ib_cat.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Items Bank Categories';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT ibcrefid,
			       ibcdesc,
			       CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END as status
		      FROM webset.disdef_bgb_itembank_cat
		     WHERE vndrefid = VNDREFID
		           ADD_SEARCH
		     ORDER BY ibcdesc
		";

        $list->addSearchField('Category', "lower(ibcdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory());

        $list->addColumn('Category', '80%');
        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'ib_cat.php';
        $list->editURL = 'ib_cat.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_itembank_cat')
                ->setKeyField('ibcrefid')
                ->applyListClassMode()
	            ->setNesting('webset.disdef_bgb_itemsbank', 'ibmrefid', 'ibcrefid', 'webset.disdef_bgb_itembank_cat', 'ibcrefid')

        );

        $list->printList();
    }
?>
