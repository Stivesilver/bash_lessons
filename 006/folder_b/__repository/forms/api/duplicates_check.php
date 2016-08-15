<?php

	Security::init();

	require_once("$g_physicalRoot/applications/webset/includes/xmlDocs.php");

	if (io::exists('key')) {
		$key = io::get('key');
		$ds = DataStorage::factory($key);
		$xml = $ds->get('xml');
	} else {
		$table = io::get('table', true);
		$key_field = io::get('key_field', true);
		$name_field = io::get('name_field', true);
		$xml_field = io::get('xml_field', true);
		$encoded = io::get('encoded', true);
		if ($encoded == '1') $xml_field = "ENCODE(DECODE(" . $xml_field . ", 'base64'), 'escape')";
		$refids = io::get('refids', true);
		$where = " AND $key_field in (" . $refids . ")";
		$xml = null;
	}

	$list = new ListClass();

	$list->title = 'Checking XML';

	if ($xml == null) {
		$list->SQL = "
			SELECT $key_field AS id,
	               $name_field AS name,
	               $xml_field AS form_xml
	          FROM $table
	         WHERE 1=1
	               $where
	         ORDER BY NAME
		";

		$list->addColumn('Name')->sqlField('name');
		$list->addColumn('XML')->sqlField('form_xml')->dataCallback('chekXML');

	} else {
		$data = array(
			0 => array(
				'Name' => 'Current Form',
				'form_xml' => $xml
			)
		);
		$list->fillData($data);

		$list->addColumn('XML')->sqlField('form_xml')->dataCallback('chekXML');
	}

	$list->printList();

	function chekXML($data) {
		if ($data['form_xml'] == '') {
			return 'blank';
		}
		$res1 = catch_dupl($data['form_xml']);
		$res2 = catchNonValid($data['form_xml']);
		$res3 = catchBadUnicode($data['form_xml']);
		$res4 = checkRC($data['form_xml']);
		$res5 = checkXMLTable($data['form_xml']);

		return $res1 . "\n" . $res2 . "\n" . $res3 . "\n" . $res4 . $res5;
	}

	function catch_dupl($txt_cont) {
		$alll = array();
		$table = '';
		if ($txt_cont) {
			if (in_array($txt_cont, $alll)) {
				$table .= "This form is <b>DUBLICATED</b>. Please check all forms with same size/length.";
			}
			$alll[] = $txt_cont;
		}

		if ((strpos(strtolower($txt_cont), "<doc") > -1 and strpos(strtolower($txt_cont), "</doc") == "")) {
			$table .= "Tag <b>DOC</b> is <b>NOT</b> closed.";
		}

		preg_match_all("/name=\".+?\"/", $txt_cont, $found_ss, PREG_PATTERN_ORDER);
		if ($txt_cont == "" or count($found_ss[0]) == 0) return $table;

		$SQL = "SELECT NULL as fldname\n";
		for ($i = 0; $i < count($found_ss[0]); $i++) {

			preg_match_all("/name=.+?\"/", $found_ss[0][$i], $fldname, PREG_PATTERN_ORDER);
			$SQL .= "UNION ALL SELECT '" . $fldname[0][0] . "' as fldname\n";
		}

		$SQL = "SELECT fldname, count(1)
	              FROM (\n $SQL \n) AS t1
	             GROUP BY fldname
	            HAVING count(1)>1";

		$result = db::execSQL($SQL);

		$table .= "<table border=1>";
		while (!$result->EOF) {
			$table .= "<tr><td>" . $result->fields[0] . "</td><td>" . $result->fields[1] . "</td></tr>";
			$result->MoveNext();
		}
		$table .= "</table>"; //    print_r($found_ss[0]); die($table);

		return $table;
	}

	function catchNonValid($xml) {
		$doc = new xmlDoc();
		$doc->xml_data = $xml;
		$errors = $doc->checkSchema();
		$result = "<table border='1'>";
		for ($i = 0; $i < count($errors); $i++) {
			$result .= "<tr><td>" . $errors[$i] . "</td></tr>";
		}
		$result .= "</table>";
		return $result;
	}

	function checkRC($xml) {
		$xmldata = stripslashes($xml);

		if (substr(strtolower(trim($xmldata)), 0, 4) != "<doc") {
			$xmldata = "<doc>" . $xmldata . "</doc>";
		}

		try {
			IDEADocument::factory($xmldata)->getSourceValidated();
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}

	}

	function checkXMLTable($xml) {

		try {
			$doc = new SimpleXMLElement($xml);
			foreach ($doc->xpath("//table") AS $tables) {
				$i = 0;
				foreach ($tables->children() AS $tr) {
					$colspan = 0;
					foreach ($tr->children() AS $td) {
						if ((int)$td['colspan']) {
							$colspan += ((int)$td['colspan'] - 1);
						}
					}
					$count = count($tr->children()) + $colspan;
					if ($i != 0 && $i != $count) {
						return 'Error in table structure on line with text: ' . (string)$tr->td;;
					}
					$i = $count;
				}
			}
		} catch (\Exception $e) {
		    return '';
		}
	}

	function catchBadUnicode($xml) {
		if (mb_detect_encoding($xml) == 'UTF-8') {
			$xml = preg_replace('/[^(\x00-\x7F)]/', "<span style='color: red;'>*</span>", $xml, -1, $count);
		} else {
			$xml = preg_replace("/<[^>]*>/", "", $xml);
			$xml = preg_replace("/\\\\\d{3}/", "<span style='color: red;'>*</span>", $xml, -1, $count);
		}
		$result = "
			<table border='1'>
				<tr>
					<td><b>%1s Bad Symbols</b></td>
					</tr>
				<tr>
					<td>%2s</td>
				</tr>
			</table>";

		if ($count > 0) {
			return sprintf($result, $count, $xml);
		} else {
			return '';
		}
	}

?>
