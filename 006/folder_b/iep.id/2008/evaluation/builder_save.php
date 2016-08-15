<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);

	$filename = SystemCore::$tempPhysicalRoot. "/" . $tsRefID.'.xml';

	if (!file_exists($filename)) {
		Security::init();
		io::err('Sorry. IEP was not archived. Please Re-Build IEP and save again.', true);
	} else {
		$xml_cont = base64_encode(file_get_contents($filename));

		$esadate = db::execSQL("
			SELECT stdevaldt
                   FROM webset.sys_teacherstudentassignment
                  WHERE tsrefid = $tsRefID
			")->getOne();

		#Add IEP
		DBImportRecord::factory('webset.es_std_esarchived', 'esarefid')
			->set('xml_cont',    $xml_cont)
			->set('stdrefid',    $tsRefID)
			->set('esadate',     $esadate)
			->set('esaname',     io::get('ReportType'))
			->set('lastuser',    db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import();
	}

	header('Location: ' . CoreUtils::getURL('builder.php', array('dskey' => $dskey)));

?>