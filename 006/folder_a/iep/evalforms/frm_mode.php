<?php

    Security::init();

    $dskey = io::post('dskey');
    $ds = DataStorage::factory($dskey);

    if (io::post('formMode') == 'text') {
        $formMode = 'text';
    } else {
        $formMode = 'pdf';
    }

    $ds->set('formMode', $formMode);
?>