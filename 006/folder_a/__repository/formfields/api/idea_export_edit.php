<?php
	Security::init();

	$RefID = io::geti('RefID');
	$mode = io::get('mode', true);
	$table = io::get('table', true);
	$key_field = io::get('key_field', true);
	$refids = io::get('refids', true);
	$dsKey = io::get('dskey', true);

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable($table, $key_field);

	$edit->title = "Add/Edit Export List";

	$edit->addGroup("General Information");

	$fields = IDEAData::getTableFields($table, false);

	foreach ($fields as $field) {
		$edit->addControl($field)->sqlField($field)->width('700px');
	}

	$edit->finishURL = CoreUtils::getURL('./idea_export_list.php', array('table' => $table, 'key_field' => $key_field, 'refids' => $refids, 'dskey' => $dsKey, 'mode' => $mode));
	$edit->cancelURL = CoreUtils::getURL('./idea_export_list.php', array('table' => $table, 'key_field' => $key_field, 'refids' => $refids, 'dskey' => $dsKey, 'mode' => $mode));

	$edit->printEdit();
?>
