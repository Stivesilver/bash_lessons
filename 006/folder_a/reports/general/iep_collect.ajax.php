<?php

	Security::init();

	CoreUtils::increaseTime();
	$selValSrt = io::get('selVal');
	$selVal = explode(',', $selValSrt);
	$all = count($selVal) + 1;
	$i = 1;
	foreach ($selVal AS $val) {
		io::progress(null, $i . ' of ' . $all, true);
		$i++;
		$SQL = "
	        SELECT siepmdocfilenm,
	               html_cont,
	               pdf_files,
	               xml_cont,
	               form_ids,
	               stdlnm,
	               stdfnm,
	               COALESCE(siepmtdesc,rptype,'IEP') AS doc,
	               TO_CHAR(siep.lastupdate, 'MM-DD-YYYY') AS lastupdate
	          FROM webset.std_iep AS siep
	          LEFT JOIN webset.std_iep_year AS iep ON iep.siymrefid = siep.iepyear
	          LEFT JOIN webset.sys_teacherstudentassignment AS ts ON siep.stdrefid = ts.tsrefid
	          LEFT JOIN webset.vw_dmg_studentmst AS std ON ts.stdrefid = std.stdrefid
	          LEFT JOIN webset.statedef_ieptypes AS types ON siep.siepmtrefid = types.siepmtrefid
		     WHERE siepmrefid = " . $val . "
	    ";

		$result = db::execSQL($SQL);
		$smfcfilename = $result->fields['siepmdocfilenm'];
		$iep_html = $result->fields['html_cont'];
		$pdf_files = $result->fields['pdf_files'];
		$form_ids = $result->fields['form_ids'];
		$file_name = $result->fields['stdfnm'] . '_' . $result->fields['stdlnm'] . '_' . str_replace(' ','_',$result->fields['doc']) . '_' . $result->fields['lastupdate'];

		SystemCore::$FS->makeDir(SystemCore::$secDisk . '/Iep_to_zip');

		if ($smfcfilename != '') {
			SystemCore::$FS->copy(SystemCore::$secDisk . '/Iep/' . $smfcfilename, SystemCore::$secDisk . '/Iep_to_zip/' . $file_name . '.pdf');
		} elseif ($result->fields['xml_cont'] != '') {
			require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php');
			require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
			$doc = new xmlDoc();
			$doc->edit_mode = 'no';
			$doc->xml_data = base64_decode($result->fields['xml_cont']);

			if (IDEACore::disParam(38) == 'N' && $form_ids == '') {
				$name = str_replace('.', '', (string)microtime(true)) . '_' . rand(0, 10000);
				FileRW::factory(SystemCore::$secDisk . '/Iep_to_zip/' . $file_name  . '.html')
					->write($doc->getHtml())
					->save(0, 'html');
			} else {
				SystemCore::$FS->copy(CoreUtils::getPhysicalPath($doc->getPdf()), SystemCore::$secDisk . '/Iep_to_zip/' . $file_name . '.pdf');
			}
		} else {
			$name = str_replace('.', '', (string)microtime(true)) . '_' . rand(0, 10000);
			FileRW::factory(SystemCore::$secDisk . '/Iep_to_zip/' . $file_name  . '.html')
				->write($iep_html)
				->save(0, 'html');
		}
	}
	io::progress(null, $i . ' of ' . $all, true);

	$result = '';
	if (SystemCore::$FS->exists(SystemCore::$tempPhysicalRoot . '/archived_iep.zip')) {
		SystemCore::$FS->remove(SystemCore::$tempPhysicalRoot . '/archived_iep.zip');
	}
	exec('cd ' . SystemCore::$secDisk . '/Iep_to_zip; zip -r ' . SystemCore::$tempPhysicalRoot . '/archived_iep.zip ' . './', $result);

	SystemCore::$FS->remove(SystemCore::$secDisk . '/Iep_to_zip');

	io::download(SystemCore::$tempPhysicalRoot . '/archived_iep.zip');
?>
