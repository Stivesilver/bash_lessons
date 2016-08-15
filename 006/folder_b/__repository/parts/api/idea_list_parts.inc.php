<?php
	function prepareLine(ListClassRow $row) {
		$row->onClick('openStdScreen(' . json_encode(CryptClass::factory()->encode($row->dataID)) . ')');
	}

	function markPastAnnual($data, $col) {
		if ($data['stdcmpltdt_real'] < date('Y-m-d') && $data['stdstatus'] == 'Y' && $data['spedstatus'] == 'Y') {
			return UILayout::factory()
				->addHTML($data[$col], '[color:red; font-weight: bold;]')
				->toHTML();
		} else {
			return $data[$col];
		}
	}

	function markPastTriennial($data, $col) {
		if (IDEAListParts::$state != 'ID') {
			if ($data['stdtriennialdt_real'] < date('Y-m-d') && $data['stdstatus'] == 'Y' && $data['spedstatus'] == 'Y') {
				return UILayout::factory()
					->addHTML($data[$col] . '', '[color:red; font-weight: bold;]')
					->toHTML();
			} else {
				return $data[$col];
			}
		} else {
			$date = mktime(0, 0, 0, date('m'), (date('m-d') == '2-29' ? '28' : '29'), date('Y') + 1);
			if ($data['stdtriennialdt_real'] < date('Y-m-d', $date) && $data['stdstatus'] == 'Y' && $data['spedstatus'] == 'Y') {
				return UILayout::factory()
					->addHTML($data[$col], '[color:red; font-weight: bold;]')
					->toHTML();
			} else {
				return $data[$col];
			}
		}
	}
?>
