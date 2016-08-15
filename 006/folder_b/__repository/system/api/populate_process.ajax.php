<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $item = unserialize($ds->get('item'));

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] != '') {

            $dbir = DBImportRecord::factory($item['destination_table'], $item['destination_refid']);

            foreach ($item['pairs'] as $pair) {
                if ($pair['as_sql']) {
                    $value = db::execSQL("
                        SELECT " . $pair['from'] . "
                          FROM " . $item['source_table'] . "
                         WHERE " . $item['source_refid'] . " = '" . db::escape(stripslashes($RefIDs[$i])) . "'
                    ")->getOne();
                    $dbir->set($pair['to'], $value);
                } else {
                    $dbir->set($pair['to'], $pair['from']);
                }
            }

            $dbir->import();
        }
    }
?>