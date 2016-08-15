<?php

    Security::init();

    if (io::get('RefID') == '') {

        $list = new ListClass();

        $list->title = 'Supplementary Services';

        $list->showSearchFields = true;

        $list->SQL = "
            SELECT dtsrefid,
                   stscode,
                   stsdesc,
                   ststext,
                   nasw,
                   CASE WHEN NOW() > enddate  THEN 'In-Active' ELSE 'Active' END  as status
              FROM webset.disdef_services_sup
             WHERE vndrefid = VNDREFID 
                   ADD_SEARCH
             ORDER BY stscode, stsdesc
        ";

        $list->addSearchField('Status', '', 'list')
            ->value('1')
            ->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
            ->data(array(1 => 'Active', 2 => 'Inactive'));

        $list->addSearchField('Service', 'stsdesc');

        $list->addColumn('Code');
        $list->addColumn('Service');
        $list->addColumn('Description');
        $list->addColumn('N/A Switcher')->type('switch');
        $list->addColumn('Status');

        $list->addURL = 'suppservice.php';
        $list->editURL = 'suppservice.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_services_sup')
                ->setKeyField('dtsrefid')
                ->applyListClassMode()
        );

        $list->printList();

        $message = 'Services will be used when 77th parameter <b>Use District Services</b> set to Yes. <br/>
                    Currently it is set to <b>' . (IDEACore::disParam(77) == 'Y' ? 'Yes' : 'No') . '</b>. 
                    See <a href="' . CoreUtils::getURL('/apps/idea/sys_maint/dis_control/vnd_control.php') . '"><b>District Parameters</b></a>.';
        print UIMessage::factory($message, UIMessage::NOTE)->toHTML();
    } else {

        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit Supplementary Services';

        $edit->setSourceTable('webset.disdef_services_sup', 'dtsrefid');

        $edit->addGroup('General Information');
        $edit->addControl('Code')->sqlField('stscode')->name('stscode')->size(12)->req();
        $edit->addControl('Service')->sqlField('stsdesc')->name('stsdesc')->size(90)->req();
        $edit->addControl('Description', 'textarea')->sqlField('ststext')->css('WIDTH', '100%')->css('HEIGHT', '50px');
        $edit->addControl(FFSwitchYN::factory('N/A Switcher'))->sqlField('nasw')->value('N');
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

        $edit->addSQLConstraint('Such Service already exists', "
            SELECT 1 
              FROM webset.disdef_services_sup
             WHERE vndrefid = VNDREFID
               AND (stscode = '[stscode]' OR stsdesc = '[stsdesc]')
               AND dtsrefid!=AF_REFID
        ");

        $edit->finishURL = 'suppservice.php';
        $edit->cancelURL = 'suppservice.php';

        $edit->firstCellWidth = "30%";

        $edit->printEdit();
    }
?>