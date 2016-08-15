<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    IDEAStudentError::checkErrors($tsRefID);

    $list = new ListClass();

    $list->title = 'Exception Manager';

    $list->multipleEdit = false;


    $list->SQL = "SELECT refid,
                         srusererrdesc,
                         lddesc,
                         isdesc,
                         ecdesc,
                         std.lastupdate,
                         srresolution
                  FROM webset.std_err std
                       INNER JOIN webset.err_systemreference sr ON sr.srrefid = std.esrefid
                       INNER JOIN webset.err_infosysdef isd ON sr.isrefid=isd.isrefid
                       INNER JOIN webset.err_categorydef cd ON sr.ecrefid=cd.ecrefid
                       INNER JOIN webset.err_leveldef ld ON sr.ldrefid=ld.ldrefid
                 WHERE stdrefid = " . $tsRefID . "
                 ORDER BY srusererrdesc, ecdesc";

    $list->addColumn('Exception Description');
    $list->addColumn('Level')->dataCallback('markCurrentYear');
    $list->addColumn('Area');
    $list->addColumn('Category');
    $list->addColumn('Log Date')->type('Date');
    $list->addColumn('Log Time')->sqlField('lastupdate')->type('time');

    $list->hideCheckBoxes = FALSE;

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_eventmst')
            ->setKeyField('semrefid')
            ->applyListClassMode()
    );

    $list->printList();

    function markCurrentYear($data, $col) {
        if ($data['lddesc'] == 'Error') {
            return UILayout::factory()
                    ->addHTML($data[$col], '[color:red; font-weight: bold;]')
                    ->toHTML();
        } else {
            return UILayout::factory()
                    ->addHTML($data[$col], '[color:green; font-weight: bold;]')
                    ->toHTML();
        }
    }

?>
