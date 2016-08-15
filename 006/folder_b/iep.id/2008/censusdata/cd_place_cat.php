<?php

    Security::init();

    $dskey   = io::get('dskey');
    $ds 	 = DataStorage::factory($dskey, true);
    $tsRefID = $ds->safeGet('tsRefID');
    $editUrl = CoreUtils::getURL('cd_place_cat_edit.php', array('dskey' => $dskey));
    $list    = new ListClass();

    $list->title           = "State LRE Reporting";
    $list->addURL          = $editUrl;
    $list->editURL         = $editUrl;
    $list->deleteTableName = "webset.std_placementcode";
    $list->deleteKeyField  = "pcrefid";
    $list->SQL             = "
        SELECT pcrefid,
               spctcode,
               plc.spcCode || ' - ' || plc.spcdesc,
               to_char(spcbeg, 'mm-dd-yyyy'),
               to_char(spcend, 'mm-dd-yyyy')
          FROM webset.std_placementcode std
               INNER JOIN webset.statedef_placementcategorycode plc ON std.spcrefid = plc.spcrefid
               INNER JOIN webset.statedef_placementcategorycodetype typ ON plc.spctrefid = typ.spctrefid
         WHERE stdrefid = $tsRefID
         ORDER BY spcbeg, pcrefid DESC
        ";

    $list->addColumn("Type")
         ->width('10%');

    $list->addColumn("Placement Category")
         ->width('40%');

    $list->addColumn("Start Date")
         ->width('15%');

    $list->addColumn("End Date")
         ->width('15%');

    $list->addColumn("LRE Form")
        ->width('20%')
        ->type("link")
        ->dataCallback('objCallBack')
        ->param("javascript:openLre('AF_REFID')");

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

    print FFInput::factory()->name('dskey')->value($dskey)->hide()->toHTML();

    function objCallBack($value) {
        return 'LRE Form (click here to edit)';
    }

?>

<script type="text/javascript">

    function openLre(placement_id) {
        url = api.url('cd_place_lre_edit.php',
                    {'placement_id': placement_id, 'dskey': $('#dskey').val()});
        api.window.open('Goal Bank Items', url);
    }

</script>