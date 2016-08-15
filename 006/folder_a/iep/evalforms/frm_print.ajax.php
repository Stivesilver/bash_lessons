<?php

    Security::init();

    $form = db::execSQL("
        SELECT mfcfilename,
               form_xml,
               xml_field_links,
               mfcdoctitle
          FROM webset.statedef_forms state
               INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
         WHERE mfcrefid = " . io::posti('mfcrefid') . "
    ")->assoc();

    $xmlvalues = '';
	foreach ($_POST as $key => $val) {
		$val = IDEAFormPDF::prepare_linebreaks($val);
		if ($val != '' and substr($key, 0, 7) == 'constr_') $values[substr($key, 7, strlen($key)) ] = stripslashes($val);
		$xmlvalues .= '<value name="' . substr($key, 7, strlen($key)) . '">' . stripslashes($val) . '</value>' . chr(10);
	}

    $student = IDEAStudent::factory(io::post('tsRefID'));
    $extension = 'pdf';
    
    if (io::get('format') == 'pdf') {
        $pdf_content = file_get_contents(SystemCore::$physicalRoot . '/applications/webset/iep/evalforms/docs/' . $form['mfcfilename']);

        $pdf_content = IDEAFormPDF::factory($pdf_content)
            ->setArchived(true)
            ->mergeFDF(IDEAFormPDF::fdf_prepare($values, io::post('smfcfilename'), io::posti('mfcrefid')))
            ->getPDFContent();

        $file_name = FileUtils::createTmpFile($pdf_content, 'pdf');
    } else {
        require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
        $doc = new xmlDoc();
        $doc->includeStyle = 'no';
        $xml_content = base64_decode($form['form_xml']);
        $xml_content = IDEAFormPDF::replace_id($xml_content, base64_decode($form['xml_field_links']));
        $doc->xml_data = $doc->xml_merge($xml_content, $xmlvalues);

        if (io::get('format') == 'html') {
            $file_name = FileUtils::createTmpFile($doc->getHtml(), 'html');
            $extension = 'html';
        } elseif (io::get('format') == 'xpdf') {
            $file_name = $_SERVER['DOCUMENT_ROOT'] . $doc->getPdf();
        } elseif (io::get('format') == 'odt') {
            $file_name = $_SERVER['DOCUMENT_ROOT'] . $doc->getOdt();
            $extension = 'odt';
        }
    }

    $nice_file_name = ucfirst(strtolower($student->get('stdlastname')));
    $nice_file_name .= '_';
    $nice_file_name .= ucfirst(strtolower($student->get('stdfirstname')));
    $nice_file_name .= '_';
    $nice_file_name .= str_replace(' ', '_', $form['mfcdoctitle']);
    $nice_file_name .= '_';
    $nice_file_name .= date('m_d_Y_h_i_s');
    $nice_file_name .= '.';
    $nice_file_name .= $extension;

    rename($file_name, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

    io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>
