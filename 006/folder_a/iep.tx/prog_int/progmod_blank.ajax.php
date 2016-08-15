<?php

	Security::init(); 

	$dskey = io::get('dskey');	
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');	
	$student = IDEAStudentTX::factory($tsRefID);
	
	require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php'); 
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.tx/prog_int/prog_build.inc.php');
	
	$file_path = SystemCore::$physicalRoot . '/uplinkos/temp/IEP_' . $tsRefID. '.pdf';

	$area = $student->getProgramInterventArea(io::get('area'));
	$progmods = $student->getProgramInterventions(io::get('area'));
	$subjects = $student->getProgramInterventSubjects();	
	
	$stdudentData = array(
		'stdname' => $student->get('stdnamefml'),
		'stdssn' => $student->get('stdschid'),
		'stddob' => $student->get('stddob'),
		'stdmeet' => $student->get('stdiepmeetingdt')		
	);
	
	buildDoc($area['area'], $stdudentData, $progmods, $subjects, $file_path); 
	
	io::download($file_path);
?>