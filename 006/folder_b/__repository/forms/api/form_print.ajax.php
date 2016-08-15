<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$fkey = io::get('RefID', TRUE);
	$fds = DataStorage::factory($fkey);

	$values_arr = array();
	$values = "<values>" . PHP_EOL;
	foreach ($_POST as $key => $val) {
		if ($val != '' and substr($key, 0, 5) == 'form_') {
			$val = IDEAFormPDF::prepare_linebreaks($val);
			$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . stripslashes($val) . '</value>' . PHP_EOL;
			$values_arr[substr($key, 5, strlen($key))] = stripslashes($val);
		}
	}
	$values .= "</values>" . PHP_EOL;

	$doc = new xmlDoc();
	$doc->includeStyle = 'no';

	$doc->xml_data = $doc->xml_merge($fds->get('template'), $values);

	if (io::get('format') == 'html') {
		$file_name = FileUtils::createTmpFile($doc->getHtml(), 'html');
		$extension = 'html';
	} elseif (io::get('format') == 'pdf') {
		$file_name = $_SERVER['DOCUMENT_ROOT'] . $doc->getPdf();
		$extension = 'pdf';
	}  elseif (io::get('format') == 'pdf_normal') {

		$pdf_content = file_get_contents($fds->get('template_pdf'));
		$pdf_content = IDEAFormPDF::factory($pdf_content)
			->setArchived(true)
			->mergeFDF(IDEAFormPDF::fdf_prepare($values_arr, null, null, $fds->get('template_pdf')))
			->getPDFContent();

		$file_name = FileUtils::createTmpFile($pdf_content, 'pdf');
		$extension = 'pdf';
	} elseif (io::get('format') == 'odt') {
		$file_name = $_SERVER['DOCUMENT_ROOT'] . $doc->getOdt();
		$extension = 'odt';
	}

	$nice_file_name = str_replace(' ', '_', $fds->get('download_file'));
	$nice_file_name .= '_';
	$nice_file_name .= date('m_d_Y_h_i_s');
	$nice_file_name .= '.';
	$nice_file_name .= $extension;

	rename($file_name, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

	io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>
