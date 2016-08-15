<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory(io::get('dskey'))->safeGet('tsRefID');
    $set_ini = IDEAFormat::getIniOptions();

    /**
      if (IDEACore::disParam(62)=="Y") {
      $script = "<script>
      location = 'cd_dis_ind.php" . $strUrlEnd . "&AMRefID=" . io::get("AMRefID") . "&ADRefID=" . io::get("ADRefID") ."';
      </script>";
      die($script);
      }
     */
    $list = new ListClass();

    $list->title = $set_ini['disability_title'] . ' Category';

    $list->SQL = "
        SELECT std.sdrefid,
               state.dccode,
               state.dcdesc,
               CASE std.sdtype
	               WHEN 1 THEN 'Primary'
	               WHEN 2 THEN 'Secondary'
	               WHEN 3 THEN 'Other'
               END
          FROM webset.std_disabilitymst std
               INNER JOIN webset.statedef_disablingcondition state ON std.dcrefid = state.dcrefid
         WHERE std.stdrefid = " . $tsRefID . "
         ORDER BY sdtype, dccode
    ";

    $list->addColumn('ID')->width('10%');
    $list->addColumn($set_ini['disability_title'])->width('65%');
    $list->addColumn('Type')->width('25%');

    $list->addURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_disabilitymst';
    $list->deleteKeyField = 'sdrefid';

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
?>