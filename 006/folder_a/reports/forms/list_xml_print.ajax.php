<?php

    Security::init();
    require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
    set_time_limit(0);

    $data = '<doc>';

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {

            $form = db::execSQL("
		        SELECT form_name,
		               form_xml,
                       values_content,
                       stdrefid
		          FROM webset.statedef_forms_xml state
		               INNER JOIN webset.std_forms_xml forms ON forms.frefid = state.frefid
		         WHERE sfrefid = " . $RefIDs[$i] . "
		    ")->assoc();
            $student = IDEAStudent::factory($form['stdrefid']);
            if ($i > 0) $data .= '<pagebreak/>';
            $data .= '<bookmark>' . htmlspecialchars(($i + 1) . '. ' . $student->get('stdlastname') . ', ' . $student->get('stdfirstname') . ' - ' . $form['form_name']) . '</bookmark>';
            $xml_content = base64_decode($form['form_xml']);
            $xml_values = base64_decode($form['values_content']);

            $doc = new xmlDoc();
            $formXML = new SimpleXMLElement($doc->xml_merge($xml_content, $xml_values));
            foreach ($formXML->children() as $block) {
                $data .= $block->asXML();
            }
            unset($formXML);
        }
        io::progress(($i + 1) * 0.9 / sizeOf($RefIDs), 'Form #' . ($i + 1) . ' of ' . sizeOf($RefIDs) . ' processed');
    }
    $data .= '</doc>';

    io::progress(0.95, 'Creating final pdf. Please wait.');

    $doc = new xmlDoc();
    $doc->xml_data = $data;
    //io::download(FileUtils::createTmpFile($data));
    io::download($_SERVER['DOCUMENT_ROOT'] . $doc->getPdf());
?>