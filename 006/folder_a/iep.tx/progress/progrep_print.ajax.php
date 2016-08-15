<?PHP

    Security::init();

    require_once(SystemCore::$physicalRoot . '/applications/webset/includes/sys_functions.php');
    require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php');

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $file_path = FileUtils::createTmpFile('', 'pdf');
    $tsRefID = $ds->safeGet('tsRefID');
    $selectedID = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
    $ESY = io::get('ESY') == 'Y' ? 'Y' : 'N';
    $stdnameFML = $ds->get('stdnamefml');
    $bgbseqordersw = db::execSQL("
        SELECT bgbseqordersw
          FROM webset.sys_teacherstudentassignment
         WHERE tsrefid = " . $tsRefID . "
    ")->getOne();
    $stdNameSize_L = '';
    $stdNameSize_R = '';
    $iepYearSize_L = '';
    $iepYearSize_R = '';
    $markPeriodSize_L = '';
    $markPeriodSize_R = '';
    $legendSize_L = '';
    $legendSize_R = '';


    $rootpdf = new doc("", "root", "", "", "", "", "", "", "", "");
    $rootpdf->set_orient('l');
    $rootpdf->set_StartHeaderPage(1);

    $rootpdf->add_toHeader('', '', 'str', 50, 'Times-Italic', 10, 'left', $stdnameFML, '', '');

    include(SystemCore::$physicalRoot . '/applications/webset/iep.tx/progress/progrep_print_core.php');
	
    printPDF($rootpdf);

    $nice_file_name = ucfirst(strtolower($ds->safeGet('stdlastname')));
    $nice_file_name .= '_';
    $nice_file_name .= ucfirst(strtolower($ds->safeGet('stdfirstname')));
    $nice_file_name .= '_';
    $nice_file_name .= 'Progress_Report';
    $nice_file_name .= '_';
    $nice_file_name .= date('m_d_Y');
    $nice_file_name .= '.pdf';

    rename($file_path, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

    io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>