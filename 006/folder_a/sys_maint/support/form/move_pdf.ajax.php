<?php

	Security::init();

	$selValSrt = io::get('selVal');
	$selVal = explode(',', $selValSrt);
	foreach ($selVal AS $val) {

		$content = db::execSQL("
			SELECT std.vndrefid,
				   uploaded_file,
				   uploaded_content
			  FROM webset.std_forms AS sf
			  	   INNER JOIN webset.sys_teacherstudentassignment AS ts ON (ts.tsrefid = sf.stdrefid)
			 	   INNER JOIN webset.dmg_studentmst AS std ON (ts.stdrefid = std.stdrefid)
			 WHERE smfcrefid = $val
		")->assocAll();
		$content = $content[0];

		$type = end(explode(".", $content['uploaded_file']));

		$filename = $val . '.' . $type;

		$secpath = explode("/", $_SESSION["s_secDisk"]);

		$secpath[count($secpath) - 1] = $content['vndrefid'];

		$secpath = implode('/', $secpath);

		$filepath = $secpath . "/Iep/" . $filename;

		if (!file_exists($filepath) and $content['uploaded_content'] != "") {
			$fp = fopen($filepath, "w");
			fwrite($fp, base64_decode($content['uploaded_content']));
			fclose($fp);
		}

		DBImportRecord::factory('webset.std_forms')
			->key('smfcrefid', $val)
			->set('uploaded_content', null)
			->set('uploaded_file', $filename)
			->import(DBImportRecord::UPDATE_ONLY);
	}
?>
