<?PHP

	Security::init();

	$brefid = io::geti('brefid');
	$bgb = db::execSQL("
		SELECT bl.stdrefid,
		       bl.esy
		  FROM webset.std_bgb_benchmark b
		       INNER JOIN webset.std_bgb_goal g ON b.grefid = g.grefid
			   INNER JOIN webset.std_bgb_baseline bl ON g.blrefid = bl.blrefid
		 WHERE brefid = " . $brefid . "
	")->assoc();
	$student = IDEAStudent::factory($bgb['stdrefid']);
	$vourefid = $student->get('vourefid');
	$school = IDEASchool::factory($vourefid);

	$pg = new UIProgressGraph();
	$pg->setSize(600, 150);
	$pg->indent(0);

	$assessment = IDEAStudentBenchmarkAssessment::factory($brefid);
	$title = $assessment->getTitle(); //title
	$vert = $assessment->getVerticalItems(); //vertical information
	$items = $assessment->calculate(); //key - trial, value - value
	# create vertical scale

	for ($i = 0; $i < count($vert); $i++) {
		$pg->addVerticalItem((string) $vert[$i], $vert[$i]);
	}

	# create graph
	foreach ($items as $key => $value) {
		$period = $school->getMarkingPeriods($value['date'], $value['date'], $bgb['esy']);
		if (count($period) > 0) {
			$mtitle = ' (MP-' . $period[1]['bmnum'] . ')';
		}else {
			$mtitle = '';
		}
		$pg->addHorizontalItem('Trial ' . $key . $mtitle, $value['result']);
	}

	//$edit->printEdit();
	rename($pg->getImagePath(), SystemCore::$tempPhysicalRoot . '/benchmark_' . session_id() . '_' . $brefid . '.jpg');
	print $pg->toHTML();
?>