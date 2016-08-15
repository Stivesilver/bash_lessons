<?php

	Security::init(NO_OUPUT);

	$dskey = io::post('dskey', TRUE);
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();

	$tsRefID = $ds->safeGet('tsRefID');

	//TEMPLATE DATA
	$form = db::execSQL("
        SELECT form_title,
               form_xml,
               xml_cont,
               archived,
			   evalforms_id
          FROM webset.es_std_evalproc_forms std
               INNER JOIN webset.es_disdef_evalforms dis ON dis.efrefid = std.evalforms_id
         WHERE frefid = " . io::posti('frefid', TRUE) . "
    ")->assoc();

	$fds->set('id', io::posti('frefid', TRUE));
	$fds->set('state_id', $form['evalforms_id']);
	$fds->set('title', $form['form_title']);
	$fds->set('template', base64_decode($form['form_xml']));
	$fds->set('values', base64_decode($form['xml_cont']));
	$fds->set('archived', $form['archived']);
	$fds->set('cancel_url', 'javascript:api.window.destroy();');
	$fds->set('finish_url', 'javascript:api.window.destroy();');
	$fds->set('save_url', CoreUtils::getURL('form_save.php', array('dskey' => $dskey)));
	$fds->set('download_file', $ds->safeGet('stdfirstname') . '_' . $ds->safeGet('stdlastname') . '_' . $form['form_title']);

	io::ajax('fkey', $fds->getKey());
?>
