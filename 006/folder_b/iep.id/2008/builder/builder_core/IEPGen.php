<?PHP

	Security::init();

	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/lib_sysparam.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.id/2008/builder/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.id/2008/builder/builder_core/IEPTemplates.php");

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$staterefid = VNDState::factory()->id;
	$IEPType = io::get('IEPType');
	$format = io::get('format') ? io::get('format') : 'PDF';
	$doc_id = '';

	if ($IEPType > 0) {
		if (io::get('idReportType') != 10) IDEAStudentRegistry::saveStdKey($tsRefID, 'id_iep', 'builder_reporttype', io::get('idReportType'), 0);
		IDEAStudentRegistry::saveStdKey($tsRefID, 'id_iep', 'builder_ieptype', $IEPType, 0);
		IDEAStudentRegistry::saveStdKey($tsRefID, 'id_iep', 'builder_contact', io::get('cont'), 0);
		IDEAStudentRegistry::saveStdKey($tsRefID, 'id_iep', 'builder_phone', io::get('phn'), 0);
		IDEAStudentRegistry::saveStdKey($tsRefID, 'id_iep', 'builder_email', io::get('email'), 0);
	}

	$doc_id = io::get('idReportType') > 0 ? io::get('idReportType') : IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'builder_reporttype', 0);

	if ($doc_id == 5) {
		$title = 'Secondary IEP';
		$stitle = 'SECONDARY IEP';
	} elseif ($doc_id == 78) {
		$title = "Early Childhood IEP";
		$stitle = "EARLY CHILDHOOD IEP";
	} elseif ($doc_id == 6) {
		$title = 'Service Plan (SP)';
		$stitle = 'SP';
	} elseif ($doc_id == 10) {
		$title = 'IEP Amendment';
		$stitle = 'IEP AMENDMENT';
	} else {
		$title = 'Individualized Education Program (IEP)';
		$stitle = 'IEP';
	}

	$leftHeader = htmlspecialchars('<b><i>' . get_field($tsRefID, 'stdName') .
		'</i></b>  <b>Current IEP</b>: <i>' . get_field($tsRefID, 'stdiepmeetingdt') .
		'</i>  <b>Projected Annual</b>: <i>' . get_field($tsRefID, 'iepreviewdt') . '</i>' .
		' <b>Current Evaluation</b>: <i>' . get_field($tsRefID, 'evaldt') .
		'</i>  <b>Projected Triennial</b>: <i>' . get_field($tsRefID, 'triennialdt') . '</i>');

	$content = '<doc headerleft="' . $leftHeader . '" headerwidthleft="100%" headerstart="1" headersize="8">';

	if (io::get('str') != '') {
		$content .= headerVnd($tsRefID);
		$content .= xmlLine('<b>' . $title . '</b>', 12, 'center');
		$content .= xmlLineBreak(1);
	}

	$otherParams = new stdClass;
	$otherParams->str = io::get('str');
	$otherParams->IEPDate = io::get('IEPDate');
	if ((int)io::get('grefid') > 0) $otherParams->grefid = io::get('grefid');

	$blocks = explode(",", $otherParams->str);
	for ($i = 0; $i < count($blocks) - 1; $i++) {
		$content .= get_block($blocks[$i], $tsRefID, $stdIEPYear, $otherParams);
	}

	//XML Forms processing
	$forms = db::execSQL("
        SELECT form_xml,
               values_content,
               form_name
          FROM webset.std_forms_xml std
               INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
               INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.MFCpRefId
         WHERE sfrefid in (" . io::get('f_str') . "0)
         ORDER BY std.lastupdate desc
    ")->assocAll();

	for ($i = 0; $i < count($forms); $i++) {
		$doc = new xmlDoc();
		$doc->edit_mode = 'no';
		$template = base64_decode($forms[$i]['form_xml']);
		$mergedDocData = $doc->xml_merge($template, base64_decode($forms[$i]['values_content']));
		$content .= '<line><section><pagebreak/></section></line>' . $mergedDocData;
	}

	$content .= "</doc>";

	$doc = new xmlDoc();
	$doc->edit_mode = 'no';
	$doc->xml_data = $content;

	//XML FILE CREATE
	$xml_file = $tsRefID . '.xml';
	file_put_contents(SystemCore::$tempPhysicalRoot . '/' . $xml_file, $doc->xml_data);
	$id = db::execSQL("
        SELECT nextval('webset.std_iep_siepmrefid_seq')
    ")->getOne();

	if ($format == 'PDF') {
		$file_name = $doc->getPdf();
	} elseif ($format == 'HTML') {
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
	$nice_file_name .= str_replace(' ', '_', $stitle);
	$nice_file_name .= '_';
	$nice_file_name .= date('m_d_Y');
	$nice_file_name .= '.';
	$nice_file_name .= strtolower($format);

	rename($_SERVER['DOCUMENT_ROOT'] . $file_name, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

	io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>
