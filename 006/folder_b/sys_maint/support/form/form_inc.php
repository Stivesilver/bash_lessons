<?php

	$area = io::get('area');
	$table = '';
	$name = '';
	$refid = '';
	$content = '';
	$base64 = '';
	$itshtml = '';

	if ($area == 'stdassess') {
		$table = 'webset.es_std_scr';
		$name = 'test_name';
		$refid = 'shsdrefid';
		$content = 'shsdhtmltext';
		$itshtml = 'yes';
	} elseif ($area == 'er_xml') {
		$table = 'webset.es_statedef_formsubsection';
		$name = 'sesadspname';
		$refid = 'sesarefid';
		$content = 'xml_body';
	} elseif ($area == 'ea_xml') {
		$table = 'webset.es_src_statedef_proc';
		$name = 'hspdesc';
		$refid = 'hsprefid';
		$content = 'xml_test';
		$base64 = 'yes';
	} elseif ($area == 'ea_html') {
		$table = 'webset.es_src_statedef_proc';
		$name = 'hspdesc';
		$refid = 'hsprefid';
		$content = 'hsphtml';
		$base64 = 'yes';
	} elseif ($area == 'ead_xml') {
		$table = 'webset.es_scr_disdef_proc';
		$name = "COALESCE((SELECT scrdesc FROM webset.es_statedef_screeningtype WHERE scrrefid = screenid LIMIT 1) || ' -> ', '') || hspdesc";
		$refid = 'hsprefid';
		$content = 'xml_test';
	} elseif ($area == 'xmlsect_vnd') {
		$table = 'webset.es_statedef_formsubsection_vnd';
		$name = 'NULL';
		$refid = 'refid';
		$content = 'xml_body';
	} elseif ($area == 'statedef_forms_xml') {
		$table = 'webset.statedef_forms_xml';
		$name = 'form_name';
		$refid = 'frefid';
		$content = 'form_xml';
		$base64 = 'yes';
	} elseif ($area == 'sped_constructions') {
		$table = 'webset.sped_constructions';
		$name = 'cnname';
		$refid = 'cnrefid';
		$content = 'cnbody';
	} elseif ($area == 'fif') {
		$table = 'webset.disdef_fif_forms';
		$name = 'fname';
		$refid = 'frefid';
		$content = 'xmlbody';
		$base64 = 'yes';
	} elseif ($area == 'eval_track_xml') {
		$table = 'webset.es_disdef_evalforms';
		$name = 'form_title';
		$refid = 'efrefid';
		$content = 'form_xml';
		$base64 = 'yes';
	} elseif ($area == 'std_iep') {
        $table = 'webset.std_iep';
        $name = 'rptype';
        $refid = 'siepmrefid';
        $content = 'xml_cont';
        $base64 = 'yes';    
    } elseif ($area == 'std_forms_xml') {
        $table = 'webset.std_forms_xml';
        $name = 'frefid';
        $refid = 'sfrefid';
        $content = 'values_content';
        $base64 = 'yes';    
    } elseif ($area == 'std_forms') {
        $table = 'webset.std_forms';
        $name = 'smfcrefid';
        $refid = 'mfcrefid';
        $content = 'fdf_content';
        $base64 = 'yes';
    } elseif ($area == 'es_std_esarchived') {
        $table = 'webset.es_std_esarchived';
        $name = 'esaname';
        $refid = 'esarefid';
        $content = 'xml_cont';
        $base64 = 'yes';
    } elseif ($area == 'es_std_join') {
        $table = 'webset.es_std_join';
        $name = 'sesarefid';
        $refid = 'esrefid';
        $content = 'xml_text';
        $base64 = 'yes';
    }
?>