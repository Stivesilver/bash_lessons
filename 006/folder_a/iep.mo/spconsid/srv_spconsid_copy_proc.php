<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$apps = array();
	$set_ini = IDEAFormat::getIniOptions();
	$xml = new SimpleXMLElement($set_ini['special_conderations_constants']);
	foreach ($xml->children() as $child) {
		$ids = explode(',', (string)$child->id);
		foreach ($ids as $id) {
			$apps[$id] = $child->template->asXML();
		}
	}

	foreach ($refIDs as $refID) {

		$answer_old = db::execSQL("
			SELECT scqrefid,
				   scarefid,
				   sscmnarrative,
				   pdf_refid,
				   saveapp,
				   syrefid
			  FROM webset.std_spconsid
			 WHERE sscmrefid = $refID
		")->assoc();

		//Link new form to Sp Consid Answer
		$id = DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
			->key('scqrefid', $answer_old["scqrefid"])
			->key('syrefid', $stdIEPYear)
			->key('stdrefid', $tsRefID)
			->set('scarefid', $answer_old["scarefid"])
			->set('sscmnarrative', $answer_old["sscmnarrative"])
			->set('saveapp', $answer_old["saveapp"])
			->set('pdf_refid', 'NULL', true)
			->setUpdateInformation()
			->import()
			->recordID();

		if ($answer_old["pdf_refid"] > 0) copyPdf($answer_old["pdf_refid"], $id, $tsRefID, $stdIEPYear);
		if (isset($apps[$answer_old["scarefid"]])) {
			copyApp($apps[$answer_old["scarefid"]], $answer_old["syrefid"], $stdIEPYear);
		}

	}

	function copyPdf($pdf_refid, $id, $tsRefID, $stdIEPYear) {

		//Get old Form details
		$form_old = db::execSQL("
			SELECT fdf_content,
				   smfcfilename,
				   smfcrefid
			  FROM webset.std_forms
			 WHERE smfcrefid = $pdf_refid
		")->assoc();

		if ($form_old["smfcrefid"] > 0) {

			//Adust Form Details
			$smfcfilename  = $form_old["smfcfilename"];
			$fdf_content  = base64_decode($form_old["fdf_content"]);
			$new_filename = "Form_". $tsRefID ."_" . date( "mdhis" ) . ".fdf";
			$new_content  = base64_encode(str_replace($smfcfilename, $new_filename, $fdf_content));

			// Copy form
			$form_new_id = DBCopyRecord::factory('webset.std_forms', 'smfcrefid')
				->key('smfcrefid', $pdf_refid)
				->set('iepyear', $stdIEPYear)
				->set('fdf_content', $new_content)
				->set('smfcfilename', $new_filename)
				->set('archived', "NULL", true)
				->copyRecord()
				->recordID();

			//Link new form to Sp Consid Answer
			DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
				->key('sscmrefid', $id)
				->set('pdf_refid', 'NULL', true)
				->setUpdateInformation()
				->import();

		} else {

			//Link new form to Sp Consid Answer
			DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
				->key('sscmrefid', $id)
				->set('pdf_refid', "NULL", true)
				->setUpdateInformation()
				->import();

		}

	}

	function copyApp($template, $sy_old, $sy_new) {

		$ideaData = IDEAData::factory();
		$data = $ideaData->xmlExport(
			$template,
			$sy_old
		);
		$ideaData->xmlImport(
			$template,
			$sy_new,
			$data
		);
	}

?>
