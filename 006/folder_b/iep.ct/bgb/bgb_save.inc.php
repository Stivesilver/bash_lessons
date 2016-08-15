<?php

	function goalCompose($RefID, &$data) {

        if (io::post('compose') == '2') {
            $data["gpreface"]    = '';
            $data["gaction"]     = '';
            $data["gcontent"]    = '';
            $data["gconditions"] = '';
            $data["gcriteria2"]  = '';
            $data["gevaluation"] = '';
            $data["gmeasure"]    = '';
            $data["gsentance"]   = '';
        } else {
            $student = IDEAStudent::factory($data["stdrefid"]);
            $data["overridetext"] = '';
            $SQL = "
                SELECT replace(gsptext, 'The student', '".
                           (IDEACore::disParam(32)=='Y'
                                ?
                                db::escape($student->get('stdfirstname'))
                                :
                                'The student'
                           )
                       ."')
                  FROM webset.disdef_bgb_goalsentencepreface
                 WHERE gsfrefid = ".$data['gpreface']."
            ";
            $gSentence = db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT gdskgaaction
                  FROM webset.disdef_bgb_ksaksgoalactions
                 WHERE gdskgarefid = ".$data['gaction']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT gdskgccontent
                  FROM webset.disdef_bgb_scpksaksgoalcontent
                 WHERE gdskgcrefid = ".$data['gcontent']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT cdesc
                  FROM webset.disdef_bgb_ksaconditions
                 WHERE crefid = ".$data['gconditions']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT dcudesc
                  FROM webset.disdef_bgb_criteriaunits
                 WHERE dcurefid IN (".$data['dcurefid'].")
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT crdesc
                  FROM webset.disdef_bgb_ksacriteria
                 WHERE crrefid = ".$data['gcriteria']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['gcriteria2']))
                $gSentence .= ' ' . $data['gcriteria2'];

            $SQL = "
                SELECT edesc
                  FROM webset.disdef_bgb_ksaeval
                 WHERE erefid = ".$data['gevaluation']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT mdesc
                  FROM webset.disdef_bgb_measure
                 WHERE mrefid = ".$data['gmeasure']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne() . '.';

            $data["gsentance"] = $gSentence;
        }
	}

    function benchCompose($RefID, &$data) {

        if (io::post('compose') == '2') {
            $data["bpreface"]    = '';
            $data["baction"]     = '';
            $data["bcontent"]    = '';
            $data["bconditions"] = '';
            $data["dcurefid"]    = '';
            $data["bcriteria"]   = '';
            $data["bcriteria2"]  = '';
            $data["bevaluation"] = '';
            $data["bmeasure"]    = '';
            $data["bsentance"]   = '';
        } else {
            $student = IDEAStudent::factory($data["stdrefid"]);
            $data["overridetext"] = '';
            $SQL = "
                SELECT replace(gsptext, 'The student', '".
                           (IDEACore::disParam(32)=='Y'
                                ?
                                db::escape($student->get('stdfirstname'))
                                :
                                'The student'
                           )
                       ."')
                  FROM webset.disdef_bgb_goalsentencepreface
                 WHERE gsfrefid = ".$data['bpreface']."
            ";
            $bSentence = db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT gdskgaaction
                  FROM webset.disdef_bgb_ksaksgoalactions
                 WHERE gdskgarefid = ".$data['baction']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['bitemslist']))
                $bSentence .= ' ' . $data['bitemslist'];

            $SQL = "
                SELECT gdskgccontent
                  FROM webset.disdef_bgb_scpksaksgoalcontent
                 WHERE gdskgcrefid = ".$data['bcontent']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT cdesc
                  FROM webset.disdef_bgb_ksaconditions
                 WHERE crefid = ".$data['bconditions']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT dcudesc
                  FROM webset.disdef_bgb_criteriaunits
                 WHERE dcurefid IN (".$data['dcurefid'].")
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT crdesc
                  FROM webset.disdef_bgb_ksacriteria
                 WHERE crrefid = ".$data['bcriteria']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['bcriteria2']))
                $bSentence .= ' ' . $data['bcriteria2'];

            $SQL = "
                SELECT edesc
                  FROM webset.disdef_bgb_ksaeval
                 WHERE erefid = ".$data['bevaluation']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT mdesc
                  FROM webset.disdef_bgb_measure
                 WHERE mrefid = ".$data['bmeasure']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne() . '.';

            $data["bsentance"] = $bSentence;
        }
    }


?>
