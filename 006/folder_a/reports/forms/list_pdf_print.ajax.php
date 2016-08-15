<?php
	Security::init();

    require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
    //set_time_limit(0);

	CoreUtils::increaseTime();

    $data = '<doc>';

	$RefIDs = explode(',', io::post('RefIDs'));
    for ($i = 0; $i < count($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {

            $form = db::execSQL("
		        SELECT mfcfilename,
		               form_xml,
		               xml_field_links,
                       fdf_content,
                       xml_content,
                       stdrefid,
                       mfcdoctitle
		          FROM webset.statedef_forms state
		               INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
		               INNER JOIN webset.std_forms forms ON forms.mfcrefid = state.mfcrefid
		         WHERE smfcrefid = " . intval($RefIDs[$i]) . "
		    ")->assoc();
		    $student = IDEAStudent::factory($form['stdrefid']);
            if ($i > 0) $data .= '<pagebreak/>';
            $data .= '<bookmark>' . htmlspecialchars(($i+1) . '. ' . $student->get('stdlastname') . ', ' . $student->get('stdfirstname') . ' - ' .  $form['mfcdoctitle']) . '</bookmark>';
            $xml_content  = base64_decode($form['form_xml']);
            $xml_values  = $form['fdf_content'] == '' ? base64_decode($form['xml_content']) : IDEAFormPDF::fdf2xml(base64_decode($form['fdf_content']), $xml_content);

            $doc = new xmlDoc();
            //io::download(FileUtils::createTmpFile($xml_content)); die();
            $formXML = new SimpleXMLElement($doc->xml_merge($xml_content, $xml_values));
            foreach ($formXML->children() as $block) {
                $data .= $block->asXML();
            }

            unset($formXML);
		}
        io::progress(($i+1)*0.9/sizeOf($RefIDs), 'Form #' . ($i+1) . ' of ' . sizeOf($RefIDs) . ' processed');
    }
    $data .= '</doc>';

    io::progress(0.95, 'Creating final pdf. Please wait.');

    $doc = new xmlDoc();
    $doc->xml_data = $data;
    //io::download(FileUtils::createTmpFile($data));
    io::download($_SERVER['DOCUMENT_ROOT'].$doc->getPdf());
?>