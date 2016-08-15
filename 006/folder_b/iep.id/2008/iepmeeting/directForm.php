<?php

    Security::init();

    $dskey = io::get('dskey', TRUE);
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $screenURL = $ds->safeGet('screenURL');

    $form = db::execSQL("
        SELECT form_name,
               form_xml,
               values_content,
               archived,
               sfrefid
          FROM webset.statedef_forms_xml state
               LEFT OUTER JOIN webset.std_forms_xml std ON state.frefid = std.frefid
               AND stdrefid = " . $tsRefID . "
               AND iepyear = " . $stdIEPYear . "
         WHERE state.frefid = " . io::geti('stateform', TRUE) . "
         ORDER BY sfrefid DESC
    ")->assoc();

	$url = IDEAForm::factory()
		->setTitle($form['form_name'])
		->setTemplate(base64_decode($form['form_xml']))
		->setValues((int) $form['sfrefid'] == 0 ? IDEAFormDefaults::factory($tsRefID)->getXML() : base64_decode($form['values_content']))
		->setUrlCancel(CoreUtils::getURL($screenURL, array('dskey' => $dskey)))
		->setUrlSave(CoreUtils::getPhysicalPath('/apps/idea/iep/evalforms_xml/frm_save.php'))
		->setUrlFinish(CoreUtils::getURL($screenURL, array('dskey' => $dskey)))
		->setPopulateButton(false)
		->setParameter('dskey', $dskey)
		->setParameter('state_id', io::geti('stateform', TRUE))
		->setParameter('std_id', $form['sfrefid'])
		->getUrlPanel();

	io::js('api.goto("' . $url . '")', true);
?>
