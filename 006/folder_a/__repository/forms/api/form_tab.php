<?php
	Security::init(NO_OUTPUT | EPS_OFF);

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	if (io::exists('xml')) {
		$xml = io::vpost('xml', DataValidator::factory('string')
			->setHTMLEntitiesPermit(true)
			->setHTMLPermit(true));
		$format = io::post('format');
		$edit = io::post('edit');
	} else {
		$fkey = io::get('fkey');
		$ds = DataStorage::factory($fkey);
		$name = $ds->get('name');
		$xml = $ds->get('xml');
		$format = io::get('format');
		$edit = io::get('edit');
	}

	$doc = new xmlDoc();

	if (substr(strtolower(trim($xml)), 0, 4) != "<doc") {
		$xml = "<doc>" . $xml . "</doc>";
	}
	$mergedDocData = stripslashes($xml);
	$doc->test_mode = "yes";
	//	se($xml); die;
	//die("<textarea name=xmldata style='width:100%; height:70%'>$mergedDocData</textarea>");

	$doc->xml_data = $mergedDocData;

	if ($edit == 1) {
		$doc->edit_mode = "yes";
	} else {
		$doc->edit_mode = "no";
	}

	if (preg_match("/applications\/webset\/support\/xml\//i", $_SERVER['HTTP_REFERER'])) {

		$dupsArr = $doc->xmlDuplicates($doc->xml_data);
		while (list($key, $val) = each($dupsArr)) {
			$dups .= $val["val"] . "<br/>";
		}

		$errors = $doc->checkSchema();
		for ($i = 0; $i < count($errors); $i++) {
			$dups .= $errors[$i];
		}
	}

	if ($format == 'pdf') {
		try {
			$pdf_file = CoreUtils::getPhysicalPath($doc->getPdf());

			$mime = FileUtils::getMIME($pdf_file);

			header('Content-Type: ' . $mime);
			header('Content-Length: ' . filesize($pdf_file));
			header('Content-Transfer-Encoding: binary;');
			header('Content-Disposition: inline; filename="' . basename($pdf_file) . '"');

			readfile($pdf_file);
		} catch (Exception $e) {
			print 'Wrong XML';
		}
	}

	if ($format == 'html') {
		try {
			print "<body>" . $doc->getHtml() . "</body>";
		} catch (Exception $e) {
			print 'Wrong XML';
		}
	}

	if ($format == 'odt') {
		rename(CoreUtils::getPhysicalPath($doc->getOdt(true)), SystemCore::$tempPhysicalRoot . '/' . basename($doc->getOdt(true)));
		io::download(SystemCore::$tempVirtualRoot . '/' . basename($doc->getOdt(true)));
	}
?>
