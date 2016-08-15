<?php

    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $list       = new ListClass();

    $list->addURL          = CoreUtils::getURL('srv_progmod_add.php',  array('dskey' => $dskey));
    $list->editURL         = CoreUtils::getURL('srv_progmod_edit.php', array('dskey' => $dskey));
    $list->title           = "Classroom Accommodations";
    $list->deleteTableName = "webset.std_srv_progmod";
    $list->deleteKeyField  = "ssmrefid";
    $list->SQL = "
        SELECT ssmrefid,
               macdesc,
               stsdesc || COALESCE(' ' ||ssmmbrother, ''),
               bcpdesc,
               TO_CHAR(webset.std_srv_progmod.ssmbegdate, 'MM/DD/YYYY'),
               ssmteacherother
          FROM webset.std_srv_progmod
               INNER JOIN webset.statedef_mod_acc acc ON webset.std_srv_progmod.stsrefid = acc.stsrefid
               LEFT OUTER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = acc.macrefid
         WHERE webset.std_srv_progmod.stdrefid = " . $tsRefID . "
           AND iepyear = $stdIEPYear
         ORDER BY cat.seq_num, stsseq, stscode, stsdesc, 1
       ";

    $list->addColumn("Category")
         ->type("group");

    $list->addColumn("Accommodations, Adaptations, or Supports");
    $list->addColumn("Location");
    $list->addColumn("Beginning Date");
    $list->addColumn("Anticipated Duration");

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_srv_progmod')
            ->setKeyField('ssmrefid')
            ->applyListClassMode('ssmrefid')
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->printList();

?>