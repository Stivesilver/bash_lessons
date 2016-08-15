<?php
	Security::init();

	$mode = io::get('mode', true);
	$refids = io::get('refids');
	$dskey = io::get('dskey');
	$export_dskey = io::get('export_dskey');
	$ds = DataStorage::factory($export_dskey);
	$table = $ds->safeGet('table');
	$key_field = $ds->safeGet('key_field');

	if ($mode == 'select') {
		$data = IDEAData::getSelects($table, $key_field, $refids);
	}
	$fields = IDEAData::getTableFields($table, false);
	$tkey = IDEAData::getKeyField($table);

	$list = new listClass();
	$list->title = 'Export List';
	$list->showSearchFields = true;

	$list->SQL = $data;

	foreach ($fields as $field) {
		$list->addColumn($field)->sqlField($field);
	}

	$list->addURL = CoreUtils::getURL('./idea_export_edit.php', array('table' => $table, 'key_field' => $key_field, 'refids' => $refids, 'dskey' => $dskey, 'mode' => $mode));
	$list->editURL = CoreUtils::getURL('./idea_export_edit.php', array('table' => $table, 'key_field' => $key_field, 'refids' => $refids, 'dskey' => $dskey, 'mode' => $mode));

	$list->deleteTableName = $table;
	$list->deleteKeyField = $tkey;

	$list->printList();

?>
