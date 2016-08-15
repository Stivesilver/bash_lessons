<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	if (io::get('RefIDs')) {
		$refids = explode(',', io::get('RefIDs'));
		$result_xml = '<DOC>';
		foreach ($refids as $refid) {
			$form = db::execSQL("
            SELECT fname,
                   xmlbody,
                   values_content,
                   s.frefid,
                   archived
              FROM webset.std_fif_forms s
                   INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
             WHERE sfrefid = " . $refid . "
        ")->assoc();

			$xml_title = $form['fname'];
			$xml_content = base64_decode($form['xmlbody']);
			$xml_values = base64_decode($form['values_content']);
			$doc = new xmlDoc();
			$xml = $doc->xml_merge($xml_content, $xml_values);
			if ($xml) {
				if ($result_xml != '<DOC>') {
					$result_xml .= '<pagebreak/>';
				}
				$xmlDoc = new SimpleXMLElement($xml);
				foreach ($xmlDoc->children() as $child) {
					$result_xml .= $child->asXML() . PHP_EOL;
				}
			}
		}
		$result_xml .= '</DOC>';
		$doc = new xmlDoc();
		$doc->xml_data = $result_xml;
		//	se($doc->getPdf());
		$nice_file_name = '504';
		$nice_file_name .= '_';
		$nice_file_name .= date('m_d_Y_h_i_s');
		$nice_file_name .= '.';
		$nice_file_name .= 'pdf';

		rename($_SERVER['DOCUMENT_ROOT'] . $doc->getPdf(), SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

		io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
	} else {
		io::msg('Please select records(s)');
	}

?>
