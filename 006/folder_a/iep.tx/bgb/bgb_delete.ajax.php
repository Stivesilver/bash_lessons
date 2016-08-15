<?php
    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    
    for ($i=0; $i < sizeOf($RefIDs); $i++) {

        if($RefIDs[$i]>0) {
            if (io::get('mode')=='baseline') {
                $SQL = "
                    UPDATE webset.std_bgb_benchmark
                       SET bprefid =  webset.std_bgb_benchmark.grefid,
                           grefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                      FROM webset.std_bgb_goal
                     WHERE webset.std_bgb_goal.grefid = webset.std_bgb_benchmark.grefid
                       AND blrefid = " . $RefIDs[$i]. ";

                    UPDATE webset.std_bgb_goal
                       SET gprefid = blrefid,
                           blrefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                     WHERE blrefid = " . $RefIDs[$i]. ";

                    UPDATE webset.std_bgb_baseline
                       SET stdschoolyear = siymrefid,
                           siymrefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                     WHERE blrefid = " . $RefIDs[$i]. ";
                ";
            } elseif (io::get('mode')=='goal'){
                $SQL = "
                    UPDATE webset.std_bgb_benchmark
                       SET bprefid = grefid,
                           grefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                     WHERE grefid = " . $RefIDs[$i]. ";

                    UPDATE webset.std_bgb_goal
                       SET gprefid = blrefid,
                           blrefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                     WHERE grefid = " . $RefIDs[$i]. ";
                ";
            } elseif (io::get('mode')=='benchmark'){
                $SQL = "
                    UPDATE webset.std_bgb_benchmark
                       SET bprefid = grefid,
                           grefid = NULL,
                           stdrefid  = NULL,
                           lastuser = '".SystemCore::$userUID."',
                           lastupdate = NOW()
                     WHERE brefid = " . $RefIDs[$i]. ";
                ";
            }
            db::execSQL($SQL);
        }
    }
?>