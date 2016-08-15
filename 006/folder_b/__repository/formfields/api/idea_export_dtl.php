<?php
	Security::init();

	$mode = io::get('mode', true);
	$refids = io::get('refids');
	$dskey = io::get('dskey');
	$export_dskey = io::get('export_dskey');
	$ds = DataStorage::factory($export_dskey);
	$table = $ds->safeGet('table');
	$key_field = $ds->safeGet('key_field');
	$nestingTable = $ds->get('nestingTable');
	$nestingRefid = $ds->get('nestingRefid');
	$foreignField = $ds->get('foreignField');
	$foreignTable = $ds->get('foreignTable');
	$foreignTableKey = $ds->get('foreignTableKey');

	if ($mode == 'insert') {
		$data = IDEAData::getInserts($table, $key_field, $refids);
	} elseif ($mode == 'update') {
		$data = IDEAData::getUpdates($table, $key_field, $refids);
	} elseif ($mode == 'select') {
		$data = IDEAData::getSelects($table, $key_field, $refids);
	} elseif ($mode == 'actual') {
		$data = DataStorage::factory($dskey)->get('SQL');
	}

	if ($nestingTable) {
		$nestingTables = json_decode($nestingTable);
		$nestingRefids = json_decode($nestingRefid);
		$foreignField = json_decode($foreignField);
		$foreignTable = json_decode($foreignTable);
		$foreignTableKey = json_decode($foreignTableKey);
		$inner = '';
		$prevTable = '';
		$prevInner = '';
		for ($i = 0; $i < count($nestingTables); $i++) {
			if ($prevTable == $foreignTable[$i]) {
				$inner = $prevInner;
			}
			$prevInner = $inner;
			$data .= "\n\n";
			$inner = ' LEFT JOIN ' . $foreignTable[$i] . ' ON (' . $nestingTables[$i] . '.' . $foreignField[$i] . ' = ' . $foreignTable[$i] . '.' . $foreignTableKey[$i] . ')' . $inner;
			if ($mode == 'insert') {
				$data .= IDEAData::getInserts($nestingTables[$i], $table . '.' . $key_field, $refids, $inner);
			} elseif ($mode == 'update') {
				$data .= IDEAData::getUpdates($nestingTables[$i], $table . '.' . $key_field, $refids, $inner);
			} elseif ($mode == 'select') {
				$data .= IDEAData::getSelects($nestingTables[$i], $table . '.' . $key_field, $refids, $inner);
			}
			$prevTable = $foreignTable[$i];
		}
	}

	print UILayout::factory()
		->newLine()
		->addObject(
			FFTextArea::factory()
				->value($data)
				->width('100%')
				->css('height', SystemCore::$coreVersion == '1' ? '542px' : '500px')
				->css('font-family', 'Courier')
				->css('font-size', '13px'),
			'100%'
		)->toHTML();
?>
