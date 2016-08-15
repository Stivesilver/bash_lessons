<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$filename = SystemCore::$tempPhysicalRoot . '/' . $tsRefID . '.xml';

	if (!file_exists($filename)) {
		Security::init();
		io::err('Sorry. IEP was not archived. Please Re-Build IEP and save again.', true);
	} else {
		$xml_cont = base64_encode(file_get_contents($filename));

		#Archive PDF Forms
		$RefIDs = explode(',', io::get('f_str'));
		for ($i = 0; $i < sizeOf($RefIDs); $i++) {
			if ($RefIDs[$i] > 0) {
				DBImportRecord::factory('webset.std_forms', 'smfcrefid')
					->key('smfcrefid', $RefIDs[$i])
					->set('archived', 'Y')
					->set('lastuser', db::escape(SystemCore::$userUID))
					->set('lastupdate', 'NOW()', true)
					->import();
			}
		}

		#Add IEP
		DBImportRecord::factory('webset.std_iep', 'siepmrefid')
			->set('xml_cont', $xml_cont)
			->set('stdrefid', $tsRefID)
			->set('siepmtrefid', db::escape(io::geti('IEPType')))
			->set('rptype', db::escape(io::geti('ReportType')))
			->set('siepmdocdate', db::escape(io::get('IEPDate')))
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import();

		db::execSQL("
			UPDATE webset.std_iep SET
	               stdIEPMeetingDT = ts.stdIEPMeetingDT,
	               stdEnrollDT     = ts.stdEnrollDT,
	               stdCmpltDT      = ts.stdCmpltDT,
	               stdEvalDT       = ts.stdEvalDT,
	               stdTriennialDT  = ts.stdTriennialDT,
	               iepyear         = iep.siymrefid
              FROM webset.sys_teacherstudentassignment ts, webset.std_iep_year iep
             WHERE ts.tsrefid = webset.std_iep.stdrefid
               AND ts.tsrefid = iep.stdrefid
               AND webset.std_iep.stdrefid = " . $tsRefID . "
	           AND siepmrefid = (SELECT max(siepmrefid) FROM webset.std_iep WHERE stdrefid = " . $tsRefID . ")
	           AND TO_CHAR(now(), 'MM-DD-YYYY') = TO_CHAR(webset.std_iep.lastupdate, 'MM-DD-YYYY')
               AND siymcurrentiepyearsw = 'Y'
		");

	}

	Header('Location: ' . CoreUtils::getURL('xml_builder.php', array('dskey' => $dskey)));
?>
