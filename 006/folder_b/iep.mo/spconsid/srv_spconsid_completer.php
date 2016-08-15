<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$SQL = "
        SELECT applurl,
               form.mfcrefid,
			   form_name,
			   form_xml,
			   fdf_content,
			   xml_field_links,
			   pdf_refid,
			   smfcfilename,
			   mfcfilename,
			   frefid
          FROM webset.std_spconsid std
               INNER JOIN webset.statedef_spconsid_quest quest ON std.scqrefid = quest.scmrefid
               INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
               LEFT OUTER JOIN webset.statedef_forms form ON form.mfcrefid = ans.formrefid
               LEFT OUTER JOIN webset.statedef_forms_xml formxml ON form.xmlform_id = formxml.frefid
			   LEFT OUTER JOIN webset.std_forms formstd ON std.pdf_refid = formstd.smfcrefid
         WHERE sscmrefid = " . io::geti('RefID') . "
    ";
	$spconsid = db::execSQL($SQL)->assoc();

	if ($spconsid['applurl'] != '') {
		$url = CoreUtils::getURL(
			SystemCore::$virtualRoot . str_replace('/applications/webset', '/apps/idea', $spconsid['applurl']), array('dskey' => $dskey, 'spconsid' => io::geti('RefID'))
		);
	} else {

		$loker = RecordLocker::factory('webset.std_spconsid', io::geti('RefID'));
		$unlockURN = $loker->getUnlockURN();
		$loker->lock();

		$template = base64_decode($spconsid['form_xml']);
		$links = base64_decode($spconsid['xml_field_links']);
		$template = IDEAFormPDF::replace_id($template, $links);

		if ($spconsid['pdf_refid'] > 0) {
			$values = IDEAFormPDF::fdf2xml(base64_decode($spconsid['fdf_content']), $template);
			$smfcfilename = $spconsid['smfcfilename'];
		} else {
			$form_state = IDEAFormTemplateXML::factory($spconsid['frefid']);
			/** @var IDEAFormDefaults $get_xml_class_name */
			$get_xml_class_name = $form_state->getClassDefaults();
			if (class_exists($get_xml_class_name) === false) {
				$get_xml_class_name = 'IDEAFormDefaults';
			}
			$obj = new $get_xml_class_name($tsRefID);
			$obj->addValues(array('strUrlEnd' => '?tsRefID=' . $tsRefID . '&mfcrefid=' . $spconsid['mfcrefid']));
			$values = $obj->getXML(); 
			$values = IDEAFormPDF::replace_id_vals($values, $links);
			$smfcfilename = 'Form_' . $tsRefID . '_' . date('mdhis') . '.fdf';
		}

		$url = IDEAForm::factory()
			->setTitle($spconsid['form_name'])
			->setTemplate($template)
			->setTemplatePDF(CoreUtils::getPhysicalPath('/applications/webset/iep/evalforms/docs/' . $spconsid['mfcfilename']))
			->setValues($values)
			->setUrlCancel('javascript:api.window.destroy();')
			->setUrlSave(CoreUtils::getPhysicalPath('srv_spconsid_form_save.php'))
			->setUrlFinish(CoreUtils::getURL('srv_spconsid_form_save.php'))
			->setParameter('dskey', $dskey)
			->setParameter('pdf_refid', $spconsid['pdf_refid'])
			->setParameter('mfcrefid', $spconsid['mfcrefid'])
			->setParameter('smfcfilename', $smfcfilename)
			->setParameter('sp_id', io::geti('RefID'))
			->addJavaScript('
				PageAPI.singleton().page
					.addEventListener(
					PageEvent.UNLOAD,
					function(e) {
						if (window.Desktop && Desktop.currentInstance) {
							Desktop.currentInstance.makeShadowRequest(' . json_encode($unlockURN) . ');
						}
					}
				);
			')
			->getUrlPanel();
	}

	header('Location: ' . $url);
?>
