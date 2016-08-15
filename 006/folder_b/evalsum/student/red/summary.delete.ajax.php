<?php
    Security::init();

    $RefIDs = explode(',', io::post('RefID'));

    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            $SQL = "
                DELETE FROM webset.es_std_redds
                 WHERE EXISTS (SELECT 1
                                 FROM webset.es_std_red red
                                      INNER JOIN webset.es_statedef_red_ds ds ON red.screening_id = ds.screening_id
                                WHERE redrefid = ".$RefIDs[$i]."
                                  AND red.stdrefid = webset.es_std_redds.stdrefid
                                  AND red.evalproc_id = webset.es_std_redds.eprefid
                                  AND ds.refid = webset.es_std_redds.dsrefid
                                  )
            ";
            db::execSQL($SQL);

            $SQL = "
                DELETE FROM webset.es_std_red WHERE redrefid = ".$RefIDs[$i]."
            ";
            db::execSQL($SQL);
        }
    }
?>
