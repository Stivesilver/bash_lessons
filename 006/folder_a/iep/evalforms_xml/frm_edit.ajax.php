<?php

    Security::init(NO_OUPUT);

    $dskey = io::post('dskey', TRUE);
    $ds = DataStorage::factory($dskey);
    $fds = DataStorage::factory();

    $tsRefID = $ds->safeGet('tsRefID');

    //TEMPLATE DATA
    $form = db::execSQL("
        SELECT form_name,
               form_xml,
               values_content,
               archived
          FROM webset.statedef_forms_xml state
               INNER JOIN webset.std_forms_xml std ON state.frefid = std.frefid
         WHERE sfrefid = " . io::posti('sfrefid', TRUE) . "
    ")->assoc();

    $fds->set('id', io::posti('sfrefid', TRUE));
    $fds->set('title', $form['form_name']);
    $fds->set('template', base64_decode($form['form_xml']));
    $fds->set('values', base64_decode($form['values_content']));
    $fds->set('archived', $form['archived']);
    $fds->set('cancel_url', CoreUtils::getURL('frm_main.php', array('dskey' => $dskey)));
    $fds->set('finish_url', CoreUtils::getURL('frm_main.php', array('dskey' => $dskey)));
    $fds->set('save_url', CoreUtils::getURL('frm_save.php', array('dskey' => $dskey)));
    $fds->set('download_file', $ds->safeGet('stdfirstname') . '_' . $ds->safeGet('stdlastname') . '_' . $form['form_name']);

    io::ajax('fkey', $fds->getKey());
?>