<?PHP

	Security::init();

	$grefid = io::geti('grefid');

	$goal = db::execSQL("
		SELECT gsentance,
		       baseline.stdrefid,
			   baseline.esy,
			   " . IDEAParts::get('baselineArea') . " as area,
			   siymrefid
		  FROM webset.std_bgb_goal goal
		       INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
			   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON baseline.blksa = ksa.gdskrefid
               INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
               INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
		 WHERE grefid = " . $grefid . "
	")->assoc();

	$student = IDEAStudent::factory((int) $goal['stdrefid']);

	$iepyear = db::execSQL("
        SELECT *
          FROM webset.std_iep_year
         WHERE siymrefid = " . (int) $goal['siymrefid'] . "
    ")->assoc();

	$school = IDEASchool::factory($student->get('vourefid'));

	$periods = $school->getMarkingPeriods($iepyear['siymiepbegdate'], $iepyear['siymiependdate'], $goal['esy']);

	$progress = db::execSQL("
        SELECT sprrefid,
               stdgoalrefid,
               stdbenchmarkrefid,
               sprnarative,
               dsyrefid,
               sprmarkingprd,
               percentofprogress,
               epsdesc
          FROM webset.std_progressreportmst std
               INNER JOIN webset.disdef_progressrepext ext ON std.eprefid = ext.eprefid
         WHERE stdgoalrefid = " . $grefid . "
		   AND stdbenchmarkrefid IS NULL
         ORDER BY stdgoalrefid, stdbenchmarkrefid, dsyrefid, sprmarkingprd
    ")->assocAll();

	$pg = new UIProgressGraph();
	$pg->setSize(600, 150);
	$pg->indent(0);


	# create vertical scale
	for ($a = 0; $a <= 100; $a += 10) $pg->addVerticalItem($a . '%', $a);

	# create graph
	foreach ($periods as $period) {
		$pg->addHorizontalItem($period['bm'], getPercent($period['bmnum'], $period['dsyrefid'], $progress));
	}

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Goal Progress Graph';
	$edit->addGroup('General Information');
	$edit->addControl('Student Name', 'protected')
		->value($student->get('stdname'));
	$edit->addControl('Area', 'protected')
		->value($goal['area']);
	$edit->addControl('Goal', 'protected')
		->value($goal['gsentance']);
	$edit->addControl('IEP Year', 'protected')
		->value(CoreUtils::formatDate($iepyear['siymiepbegdate'], 'm-d-Y') . ' / ' . CoreUtils::formatDate($iepyear['siymiependdate'], 'm-d-Y'));
	$edit->addControl('Goal Graph', 'protected')
		->value($pg->toHTML());

	$edit->cancelURL = 'javascript:api.window.destroy();';

	//$edit->printEdit();
	rename($pg->getImagePath(), SystemCore::$tempPhysicalRoot . '/goal_' . $grefid . '.jpg');
	print $pg->toHTML();

	function getPercent($index, $dsy, $progress) {
		for ($i = 0; $i < count($progress); $i++) {
			if ($progress[$i]['dsyrefid'] == $dsy && $progress[$i]['sprmarkingprd'] == $index) {
				return $progress[$i]['percentofprogress'];
			}
		}
		return 0;
	}

?>
