<?PHP

	Security::init();

	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.mo/documentation/builder_core/IEPDates.php');
	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/lib_sysparam.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.il/2010/builder/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.il/2010/builder/builder_core/IEPTemplates.php");

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$staterefid = VNDState::factory()->id;
	$backgr_color = "c0c0c0";
	$IEPType = io::get('IEPType');
	$format = io::get('format') ? io::get('format') : 'PDF';

	$hblk = explode(',', io::get('headers'));
	array_pop($hblk);

	$allblocks = IDEAFormat::getDocBlocks();
	for ($i = 0; $i < count($allblocks); $i++) {
		$bNames[$allblocks[$i]['iepnum']] = $allblocks[$i]['iepdesc'];
	}

	//IEP TYPE SAVE and READ
	if ($IEPType == '') {
		$IEPType = IDEAStudentRegistry::readStdKey($tsRefID, 'mo_iep', 'builder_ieptype', 0);
	} else {
		IDEAStudentRegistry::saveStdKey($tsRefID, 'mo_iep', 'builder_ieptype', pg_escape_quotes($IEPType), 0);
	}

	//TOP MARGIN
	$std_demo = stdDemo($tsRefID);

	$leftHeader = htmlspecialchars('<b><i>' . ucfirst(strtolower($std_demo['stdlnm'])) . ', ' . ucfirst(strtolower($std_demo['stdfnm'])) .
		'</i></b>  <b>IEP Meeting</b>: <i>' . get_field($tsRefID, 'stdiepmeetingdt') .
		'</i>  <b>Projected Annual</b>: <i>' . get_field($tsRefID, 'iepreviewdt') . '</i>');

	$rightHeader = htmlspecialchars('<b>Current Evaluation</b>: <i>' . get_field($tsRefID, 'evaldt') .
		'</i>  <b>Projected Triennial</b>: <i>' . get_field($tsRefID, 'triennialdt') . '</i>');

	$landscape = io::get('landscape') == 'yes' ? ' orient="landscape"' : '';

	$content = '<doc headerleft="' . $leftHeader . '" headerright="' . $rightHeader . '" headerstart="1" headersize="8"' . $landscape. '>';

	if (io::get('draft') == "yes") {
		$content .= '<watermark>DRAFT</watermark>';
	}

	$otherParams = array();
	$otherParams["str"] = io::get('str');
	$otherParams["IEPType"] = $IEPType;
	$otherParams["ReportType"] = io::get('ReportType');

	$strblocks = io::get('str');
	$last = substr($strblocks, -1);
	if ($last == ',') {
		$strblocks = substr($strblocks, 0, -1);
	}

	$blocks = explode(",", $strblocks);
	for ($i = 0; $i <= count($blocks) - 1; $i++) {
		if (in_array($blocks[$i], $hblk)) {
			$header = true;
		} else {
			$header = false;
		}
		$content .= get_block($blocks[$i], $tsRefID, $stdIEPYear, $header, $otherParams);
	}

	//Progess Report
	if (io::get('prog_rep') == 'yes') {
		if (io::get('str') != "") $content .= '<pagebreak/>';
		$content .= '<line cellspacing="0">
	                <section bgcolor="' . $backgr_color . '" size="22"><b>WeBSET: Student Progress Report</b></section>
                </line>
                <bookmark>Progress Report</bookmark>
	            <line cellspacing="0"><section bgcolor="' . $backgr_color . '" size="5"></section></line>
                <line cellspacing="0">
	                <section bgcolor="' . $backgr_color . '" size="18"><i>' . htmlspecialchars(SystemCore::$VndName) . '</i></section>
                </line>
	            <line cellspacing="0"><section bgcolor="' . $backgr_color . '" size="5"></section></line>';
		include(SystemCore::$physicalRoot . "/applications/webset/iep/documentation/xmlReport.php");
		$content .= getProgres($tsRefID, $stdIEPYear);
	}

	//Forms processing
	$SQL = "
        SELECT smfcrefid,
               mfcfilename,
               smfcfilename,
               mfcdoctitle,
               fdf_content,
               form_xml,
               xml_content,
               xml_field_links
	      FROM webset.std_forms std
               INNER JOIN webset.statedef_forms state ON state.mfcrefid = std.mfcrefid
               LEFT OUTER JOIN webset.statedef_forms_xml xml ON xmlform_id = frefid
		 WHERE smfcrefid in (" . io::get('f_str') . "0)
		 ORDER BY std.lastupdate desc
    ";
	$result = db::execSQL($SQL);
	$g = -10000;
	while (!$result->EOF) {

		$doc = new xmlDoc();
		$doc->edit_mode = "no";
		$template = base64_decode($result->fields["form_xml"]);

		if ($result->fields["xml_content"]) {
			$mergedDocData = $doc->xml_merge($template, base64_decode($result->fields["xml_content"]));
		} else {
			$template = IDEAFormPDF::replace_id($template, base64_decode($result->fields["xml_field_links"]));
			$xml_values = IDEAFormPDF::fdf2xml(base64_decode($result->fields["fdf_content"]), $template);
			$mergedDocData = $doc->xml_merge($template, $xml_values);
		}

		$content .= '<pagebreak/><bookmark>Form: ' . htmlspecialchars($result->fields["mfcdoctitle"]) . '</bookmark>' . $mergedDocData;
		$result->MoveNext();
	}

	$content .= "</doc>";

	//die($content);

	$doc = new xmlDoc();
	$doc->edit_mode = "no";
	$doc->xml_data = $content;

	if (io::get('ReportType') == 14) {
		$doc->xml_data = str_replace("IEP", "SP ", $doc->xml_data);
	}

	//XML FILE CREATE
	$xml_file = $tsRefID . '.xml';
	file_put_contents(SystemCore::$tempPhysicalRoot . '/' . $xml_file, $doc->xml_data);
	$id = db::execSQL("select nextval('webset.std_iep_siepmrefid_seq')")->getOne();

	if ($format == 'PDF' or $pdfforms != '') {
		$file_name = $doc->getPdf();
	} elseif ($format == 'HTML' or ($format == '' and IDEACore::disParam(38) == 'N')) {
		$pdfforms = '';
		$file_name = SystemCore::$tempVirtualRoot . '/' . $id . '.html';
		$filepath = SystemCore::$tempPhysicalRoot . '/' . $id . '.html';
		file_put_contents($filepath, $doc->getHtml());
	} elseif ($format == 'ODT') {
		$file_name = $doc->getOdt();
	} else {
		$file_name = $doc->getPdf();
	}

	$nice_file_name = ucfirst(strtolower($ds->safeGet('stdlastname')));
	$nice_file_name .= '_';
	$nice_file_name .= ucfirst(strtolower($ds->safeGet('stdfirstname')));
	$nice_file_name .= '_';
	$nice_file_name .= str_replace(' ', '_', $IEPType);
	$nice_file_name .= '_';
	$nice_file_name .= date('m_d_Y');
	$nice_file_name .= '.';
	$nice_file_name .= strtolower($format);

	rename($_SERVER['DOCUMENT_ROOT'] . $file_name, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

	io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>
