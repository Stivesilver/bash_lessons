<?php

	function goalCompose($RefID, &$data) {

        if (io::post('compose') == '2') {
            $data["gpreface"]    = '';
            $data["gaction"]     = '';
            $data["gcontent"]    = '';
            $data["gconditions"] = '';
            $data["dcurefid"]    = '';
            $data["gcriteria"]   = '';
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
                 WHERE gsfrefid = ".(int)$data['gpreface']."
            ";
            $gSentence = db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT gdskgaaction
                  FROM webset.disdef_bgb_ksaksgoalactions
                 WHERE gdskgarefid = ".(int)$data['gaction']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT gdskgccontent
                  FROM webset.disdef_bgb_scpksaksgoalcontent
                 WHERE gdskgcrefid = ".(int)$data['gcontent']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT cdesc
                  FROM webset.disdef_bgb_ksaconditions
                 WHERE crefid = ".(int)$data['gconditions']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT dcudesc
                  FROM webset.disdef_bgb_criteriaunits
                 WHERE dcurefid = ".(int)$data['dcurefid']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT crdesc
                  FROM webset.disdef_bgb_ksacriteria
                 WHERE crrefid = ".(int)$data['gcriteria']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['gcriteria2']))
                $gSentence .= ' ' . $data['gcriteria2'];

            $SQL = "
                SELECT edesc
                  FROM webset.disdef_bgb_ksaeval
                 WHERE erefid = ".(int)$data['gevaluation']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT mdesc
                  FROM webset.disdef_bgb_measure
                 WHERE mrefid = ".(int)$data['gmeasure']."
            ";
            $gSentence .= ' ' . db::execSQL($SQL)->getOne() . '.';

            $data["gsentance"] = $gSentence;
        }
	}

    function goalProgess($RefID, &$data) {
        #MO State only
        file_put_contents(SystemCore::$tempPhysicalRoot . "/compose.txt", io::post('meas_id'));
        if (io::post('meas_id') != '') {
            $ids = explode(",", io::post('meas_id'));
            $SQL = "
                SELECT refid
                  FROM webset.glb_validvalues
                 WHERE valuename = 'MOBGBProgressMeasurement'
                   AND validvalue ILIKE 'Other%'
            ";
			$other_def_id = db::execSQL($SQL)->getOne();
            $SQL = "
                DELETE FROM webset.std_bgb_goal_meas WHERE goal_refid = ".$RefID."
            ";
            db::execSQL($SQL);
            for ($i = 0; $i < count($ids); $i++) {
                if ($ids[$i] > 0) {
                    DBImportRecord::factory('webset.std_bgb_goal_meas', 'refid')
                        ->set('goal_refid', $RefID)
                        ->set('meas_refid', $ids[$i])
                        ->set('other', ($other_def_id == $ids[$i] ? io::post('meas_other') : ''))
                        ->import();
                }
            }
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
                 WHERE gdskgarefid = ".(int)$data['baction']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['bitemslist']))
                $bSentence .= ' ' . $data['bitemslist'];

            $SQL = "
                SELECT gdskgccontent
                  FROM webset.disdef_bgb_scpksaksgoalcontent
                 WHERE gdskgcrefid = ".(int)$data['bcontent']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT cdesc
                  FROM webset.disdef_bgb_ksaconditions
                 WHERE crefid = ".(int)$data['bconditions']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT dcudesc
                  FROM webset.disdef_bgb_criteriaunits
                 WHERE dcurefid = ".(int)$data['dcurefid']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT crdesc
                  FROM webset.disdef_bgb_ksacriteria
                 WHERE crrefid = ".(int)$data['bcriteria']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            if (isset($data['bcriteria2']))
                $bSentence .= ' ' . $data['bcriteria2'];

            $SQL = "
                SELECT edesc
                  FROM webset.disdef_bgb_ksaeval
                 WHERE erefid = ".(int)$data['bevaluation']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne();

            $SQL = "
                SELECT mdesc
                  FROM webset.disdef_bgb_measure
                 WHERE mrefid = ".(int)$data['bmeasure']."
            ";
            $bSentence .= ' ' . db::execSQL($SQL)->getOne() . '.';

            $data["bsentance"] = $bSentence;
        }
    }


?>
