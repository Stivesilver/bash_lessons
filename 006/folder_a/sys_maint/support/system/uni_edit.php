<?php
	Security::init(MODE_ADMIN);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$sql = $ds->safeGet('sql');
	$table = io::get('table');
	$db = io::geti('db');

	if ($sql) {
		$dbparam = DBUtils::factory()->getConnectionInfo($db);
		$dbcon = new DBPostgres($dbparam->ip, $dbparam->login, $dbparam->password, $dbparam->name);
		$data = DBUtils::factory($dbcon)->execSQL($sql)->columns();

		$edit = new EditClass('edit1', io::geti('RefID'), $dbcon);

		$edit->title = 'Add/Edit Case Notes';

		$edit->setSourceTable($table, $data[0]);

		$edit->addGroup('Table content');

		foreach ($data as $item) {
			$edit->addControl(FFTextArea::factory($item))
				->sqlField($item)
				->autoHeight(true);
		}

		$edit->topButtons = true;

		$edit->finishURL = CoreUtils::getURL('./uni_list.php', array('dskey' => $dskey, 'db' => $db));
		$edit->cancelURL = CoreUtils::getURL('./uni_list.php', array('dskey' => $dskey, 'db' => $db));

		$edit->saveAndEdit = true;

		$edit->printEdit();
	}
?>
