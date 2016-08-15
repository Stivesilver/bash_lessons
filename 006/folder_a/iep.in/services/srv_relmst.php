<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $list = new ListClass();

    $list->title = 'Related Service';

    $list->SQL = "
        SELECT ssmrefid,
               strdesc,              
               ssmbegdate,
               COALESCE(ssmtime || '  ','') ||  amt.sadesc || ' ' ||  freq.sfdesc,               
               COALESCE(ssmclasstypenarr, crtdesc),
               umfirstname || ' ' || umlastname,
               nasw
          FROM webset.std_srv_rel std
               INNER JOIN webset.disdef_services_rel dis ON std.dtrrefid = dis.dtrrefid
               INNER JOIN webset.def_spedfreq freq ON std.ssmfreq = freq.sfrefid
               INNER JOIN webset.def_spedamt amt ON std.ssmamt = amt.sarefid
               INNER JOIN webset.def_classroomtype class ON std.ssmclasstype = class.crtrefid
               LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
         WHERE std.stdrefid = " . $tsRefID . "
         ORDER BY 2
    ";

    $list->addColumn('Service');    
    $list->addColumn('Beginning Date')->type('date')->dataCallback('clearNAservice');
    $list->addColumn('Frequency')->dataCallback('clearNAservice');
    $list->addColumn('Location')->dataCallback('clearNAservice');
    if (IDEACore::disParam(106) != 'N') $list->addColumn('Implementor')->dataCallback('clearNAservice');

    $list->addURL = CoreUtils::getURL('srv_relmst_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_relmst_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_srv_rel';
    $list->deleteKeyField = 'ssmrefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->printList();

    function clearNAservice($data, $col) {
        if ($data['nasw'] == 'Y') {
            return '';
        } else {
            return $data[$col];
        }
    }
?>

