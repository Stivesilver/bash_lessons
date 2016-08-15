<?PHP

	Security::init();

	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.mo/documentation/builder_core/IEPDates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.in/documentation/builder_core/IEPDates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.in/documentation/builder_core/IEPTemplates.php');
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.in/documentation/iep_blocks.php');
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

	$root = new blocks("", "");
	$rootpdf = new doc('', 'root', '', '', '', '', '', '', '', '');
	$rootpdf->set_orient('p');

	if (!(strstr($IEPType, "Draft") === false)) {
		$rootpdf->set_StartWaterMarkPage(2);
		$rootpdf->add_watermark('DRAFT', "Helvetica-Bold", "200", "F5F5F5", "F00000", "left");
	}

	if ($mode == 'archive') {
		if (!(strstr($IEPType, "not held")) === false) {
			$rootpdf->set_StartWaterMarkPage(1);
			$rootpdf->add_watermark('Conference not held', "Times-Bold", "90", "bdbdbd", "F00000", "left");
		} else {
			$rootpdf->set_StartWaterMarkPage(1);
			$rootpdf->add_watermark('ARCHIVED', "Times-Bold", "130", "bdbdbd", "F00000", "left");
		}
	}

	if (!in_array("0", $ablk)) {
		$rootpdf->set_StartHeaderPage(1);
	}

	$rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'left', get_field($tsRefID, "stdName"), '', '');
	$rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'right', get_field($tsRefID, "stdIEPMeetingDT"), '', '');
	$rootpdf->set_StartHeaderPage(2);

	$g1 = -101;
	$g2 = -102;
	$g3 = -103;
	$g4 = -104;
	$g5 = -105;

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
			$root = get_block($bOrder[$i], $tsRefID, $stdIEPYear, $otherParams);
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
