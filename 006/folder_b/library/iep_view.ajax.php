<?php
	Security::init();
	error_reporting(0);
	$SQL = "
        SELECT siepmdocfilenm,
               html_cont,
               pdf_files,
               xml_cont,
               form_ids
          FROM webset.std_iep
	     WHERE siepmrefid = " . io::geti('RefID') . "
    ";

	$result = db::execSQL($SQL);
	$smfcfilename = $result->fields['siepmdocfilenm'];
	$iep_html = $result->fields['html_cont'];
	$pdf_files = $result->fields['pdf_files'];
	$form_ids = $result->fields['form_ids'];

	if ($iep_html)
		$iep_html = preg_replace_callback("/<img.+?>/i", "set_blank", $iep_html);

	function set_blank($txt) {
		$with_nine = $txt[0]; //die($txt[0]);
		if (str_replace('off', '', $with_nine) != $with_nine) {
			$with_nine = '<input type=checkbox>';
		} else {
			$with_nine = '<input type=checkbox checked>';
		}
		return $with_nine;
	}

	if ($smfcfilename != '') {
		io::download(SystemCore::$secDisk . '/Iep/' . $smfcfilename);
	} elseif ($result->fields['xml_cont'] != '') {
		require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php');
		require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
		$doc = new xmlDoc();
		$doc->edit_mode = 'no';
		$doc->xml_data = base64_decode($result->fields['xml_cont']);

		if (IDEACore::disParam(38) == 'N' && $form_ids == '') {
			io::download(FileUtils::createTmpFile($doc->getHtml(), 'html'));
		} else {
			io::download($_SERVER['DOCUMENT_ROOT'] . $doc->getPdf());
		}
	} else {
		io::download(FileUtils::createTmpFile($iep_html, 'html'));
	}
?>
