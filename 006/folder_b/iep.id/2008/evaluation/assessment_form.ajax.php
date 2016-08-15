<?php

	# get html form for edit-page.
	Security::init();

	$id         = io::post('id');
	$ds         = DataStorage::factory(io::post('dskey'));
	$RefID      = io::post('RefID');
	$xml_data   = io::post('xml_data');
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	if ($xml_data) {
		$values = base64_decode($xml_data);
	} else {
		$values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}

	$SQL = "
		SELECT xml_test,
               hspdesc,
               sumdata_sw
          FROM webset.es_scr_disdef_proc
         WHERE hsprefid = $id
     	";

	$result = db::execSQL($SQL)->assoc();

	$url = IDEAForm::factory()
		->setTitle($result['hspdesc'])
		->setTemplate($result['xml_test'])
		->setValues($values)
		->setUrlCancel('javascript:api.window.destroy();')
		->setUrlSave(CoreUtils::getPhysicalPath('assessment_form_save.php'))
		->setUrlFinish(CoreUtils::getURL('assessment_form_save.php'))
		->getUrlPanel();

	io::ajax('caption', 'Complete Form');
	io::ajax('url',     $url);
	io::ajax('values', $values);
	io::ajax('ref', $RefID);

?>
