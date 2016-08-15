<?php
	Security::init(MODE_ADMIN);
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$sql = $ds->safeGet('sql');
	$db = io::geti('db');

	if ($sql) {

		$dbparam = DBUtils::factory()->getConnectionInfo($db);
		$dbcon = new DBPostgres($dbparam->ip, $dbparam->login, $dbparam->password, $dbparam->name);

		$list = new ListClass('', $dbcon);

		$list->SQL = $sql;

		$data = DBUtils::factory($dbcon)->execSQL($sql)->columns();
		$pkey = $data[0];

		foreach ($data as $item) {
			$list->addSearchField($item)->sqlField($item);
			$list->addColumn($item)->sqlField($item);
		}
		$tableName = IDEAData::tableName($data, $sql, $dbcon);
		if ($tableName) {
			$list->deleteTableName = $tableName;
			$list->deleteKeyField = $pkey;

			$list->addButton(
				FFIDEAExportButton::factory()
					->setTable($list->deleteTableName)
					->setKeyField($list->deleteKeyField)
					->applyListClassMode()
			);
			$list->addURL = CoreUtils::getURL('./uni_edit.php', array('dskey' => $dskey, 'table' => $tableName, 'db' => $db));
			$list->editURL = CoreUtils::getURL('./uni_edit.php', array('dskey' => $dskey, 'table' => $tableName, 'db' => $db));
		}
		$list->printable = true;

		$list->printList();
	}

?>
