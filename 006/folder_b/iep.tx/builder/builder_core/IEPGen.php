<?PHP

	Security::init();

	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.mo/documentation/builder_core/IEPDates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.tx/builder/builder_core/IEPDates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.tx/builder/builder_core/IEPTemplates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.tx/builder/iep_blocks.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/includes/translate.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
	require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php');
	require_once(SystemCore::$physicalRoot . '/uplinkos/classes/lib_sysparam.php');

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = IDEAStudent::factory($tsRefID);
	$stdIEPYear = $student->get('stdiepyear');
	$str = substr(io::get('str'), 0, strlen(io::get('str')) - 1);
	$file_path = SystemCore::$physicalRoot . '/uplinkos/temp/IEP_' . $tsRefID . '.pdf';
	$mode = io::get('mode');
	$IEPDate = io::get('IEPDate');
	$ReportType = io::get('ReportType');
	$IEPType = io::get('IEPType');
	$set_ini = IDEAFormat::getIniOptions();

	$backgr_color = '7e7e7e';

	$ablk = explode(',', $str);

	//$root = new blocks("", "");
	$rootpdf = new doc('', 'root', '', '', '', '', '', '', '', '');
	$rootpdf->set_orient('p');

	if (!in_array("0", $ablk)) {
		$rootpdf->set_StartHeaderPage(1);
	}

	$rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'left', get_field($tsRefID, "stdName"), '', '');
	$rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'right', get_field($tsRefID, "stdIEPMeetingDT"), '', '');
	$rootpdf->set_StartHeaderPage(2);

	if (($ReportType and $str != 31) or count($ablk) > 2) {
		$doc = new xmlDoc();
		$doc->xml_data = headerVnd($IEPType, $ReportType);
		$doc->addToPdf();
	}

	$otherParams = array();
	$otherParams["str"] = $str;
	$otherParams["IEPDate"] = $IEPDate;
	$otherParams["ReportType"] = $ReportType;
	$otherParams["g_physicalRoot"] = SystemCore::$physicalRoot;
	foreach ($set_ini as $key => $value) {
		$otherParams[$key] = $value;
	}

	for ($i = 0; $i < count($bOrder); $i++) {
		if (in_array($bOrder[$i], $ablk)) {
			$root = get_block_tx($bOrder[$i], $tsRefID, $stdIEPYear, $otherParams);
		}
	}

	printPDF($rootpdf);

	$nice_file_name = ucfirst(strtolower($student->get('stdlastname')));
	$nice_file_name .= '_';
	$nice_file_name .= ucfirst(strtolower($student->get('stdfirstname')));
	$nice_file_name .= '_';
	$nice_file_name .= str_replace(' ', '_', $IEPType);
	$nice_file_name .= '_';
	$nice_file_name .= date('m_d_Y');
	$nice_file_name .= '.pdf';

	if ($mode != 'archive') {
		copy($file_path, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));
		io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
	}
?>