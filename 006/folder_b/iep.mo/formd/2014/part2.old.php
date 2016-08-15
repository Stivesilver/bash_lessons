<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $list = new ListClass();

    $list->title = 'Form D - Part 2: State Accommodations';

    $list->SQL = "SELECT std.refid,
                         progdesc,
                         catdesc,
                         COALESCE(acccode || ' - ', '') || ' ' || CASE LOWER(SUBSTRING(accdesc,0,6)) = 'other' WHEN TRUE THEN accdesc ||' '|| COALESCE(std.acc_oth,'') ELSE accdesc END
                    FROM webset.statedef_aa_acc acc
                         INNER JOIN webset.statedef_aa_cat ON webset.statedef_aa_cat.catrefid = acc.acccat
                         INNER JOIN webset.statedef_aa_prog sbj ON sbj.code = acc.cat
                         INNER JOIN webset.std_form_d_acc std ON acc.accrefid = std.accrefid
                   WHERE stdrefid = " . $tsRefID . "
                     AND syrefid = " . $stdIEPYear . "
                   ORDER BY sbj.seqnum, webset.statedef_aa_cat.catrefid, acc.seq_num";

    $list->addColumn('Subject')->type('group');
    $list->addColumn('Category');
    $list->addColumn('Accommodation');

    $list->deleteTableName = 'webset.std_form_d_acc';
    $list->deleteKeyField = 'refid';

    $list->addURL = CoreUtils::getURL('part2add.php', array('dskey' => $dskey));
//    $list->editURL = CoreUtils::getURL('part2add.php', array('dskey' => $dskey));

	$list->editURL = "javascript:editForm('AF_REFID', " . json_encode($dskey) . ");";

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

    include("notes2.php");
?>

<script>
	function editForm(refid, dskey) {
		var wnd = api.desktop.open('Add/Edit State Accommodations', api.url('./part2add.php', {refid: refid, dskey: dskey}));
	}
</script>
