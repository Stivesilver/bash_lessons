<?PHP

    Security::init();

    require_once(SystemCore::$physicalRoot . '/applications/webset/iep.mo/documentation/builder_core/IEPDates.php');
    require_once(SystemCore::$physicalRoot . '/applications/webset/iep.ks/documentation/builder_core/IEPDates.php');
    require_once(SystemCore::$physicalRoot . '/applications/webset/iep.ks/documentation/builder_core/IEPTemplates.php');
    require_once(SystemCore::$physicalRoot . '/applications/webset/iep.ks/documentation/iep_blocks.php');
    require_once(SystemCore::$physicalRoot . '/applications/webset/includes/xmlDocs.php');
    require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php');

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $str = substr(io::get('str'), 0, strlen(io::get('str')) - 1);
    $file_path = SystemCore::$physicalRoot . '/uplinkos/temp/IEP_' . $tsRefID . '.pdf';
    $mode = io::get('mode');
    $IEPDate = io::get('IEPDate');
    $ReportType = io::get('ReportType');
    $IEPType = io::get('IEPType');

    $backgr_color = '7e7e7e';

    $ablk = explode(',', $str);

    $rootpdf = new doc('', 'root', '', '', '', '', '', '', '', '');
    $rootpdf->set_orient('p');

    $pos = strpos($IEPType, 'Draft');
    if (!($pos === false)) {
        $rootpdf->set_StartWaterMarkPage(2);
        $rootpdf->add_watermark('DRAFT', 'Helvetica-Bold', '200', 'F5F5F5', 'F00000', 'left');
    }
    if ($ReportType == 'Transfer IEP') {
        $rootpdf->set_StartWaterMarkPage(1);
        $rootpdf->add_watermark('Transfer IEP', 'Times-Bold', '130', 'bdbdbd', 'F00000', 'left');
    }

    if ($IEPType == 'Transfer IEP') {
        $rootpdf->set_StartWaterMarkPage(1);
        $rootpdf->add_watermark('Transfer IEP', 'Times-Bold', '130', 'bdbdbd', 'F00000', 'left');
    }


    if (!in_array('0', $ablk)) {
        $rootpdf->set_StartHeaderPage(1);
    }

    $rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'left', get_field($tsRefID, 'stdName'), '', '');
    $rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'right', 'Date of IEP: ' . get_field($tsRefID, 'stdIEPMeetingDT'), '', '');

    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Bold', 5, '', ' ', '', '');
    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Bold', 25, '', get_field($tsRefID, 'stdDADistrict', $_SESSION['s_VndRefID']), 'ffffff', $backgr_color);
    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Roman', 1, '', ' ', 'ffffff', $backgr_color);
    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 70, 'Times-Italic', 16, '', 'Special Education Department', 'ffffff', $backgr_color);
    $tmp = $rootpdf->add_toLine($l, '', 'str', 30, 'Times-Roman', 16, 'center', $IEPType, 'ffffff', $backgr_color);
    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Roman', 6, '', ' ', 'ffffff', $backgr_color);

    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Bold', 2, '', ' ', '', '');

    $a = docDetails($drefid);

    $l = &$rootpdf->add_Line();
    $tmp = $rootpdf->add_toLine($l, '', 'str', 100, 'Times-Bold', 18, '', $a['docdesc'], '', '');
    $blockSeq = 0;

    for ($i = 0; $i < count($bOrder); $i++) {
        if (in_array($bOrder[$i], $ablk)) {
            $root = get_block_ks($bOrder[$i], $tsRefID, $stdIEPYear, $blockSeq);
            $blockSeq++;
        }
    }

    printPDF($rootpdf);

    $nice_file_name = ucfirst(strtolower($ds->safeGet('stdlastname')));
    $nice_file_name .= '_';
    $nice_file_name .= ucfirst(strtolower($ds->safeGet('stdfirstname')));
    $nice_file_name .= '_';
    $nice_file_name .= str_replace(' ', '_', $IEPType);
    $nice_file_name .= '_';
    $nice_file_name .= date('m_d_Y');
    $nice_file_name .= '.pdf';

    copy($file_path, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

    io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>