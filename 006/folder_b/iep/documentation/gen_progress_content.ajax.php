<?php

	Security::init();

	$refid = io::post('refid');
	$dskey = io::post('dskey');
	$esy = io::post('esy');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$type = io::get('type');

	$student = new IDEAStudent($tsRefID, $siymrefid);
	$bgbreports = $student->getProgressReportSimpleBGB($esy);

	$ds->set('bgbreports', $bgbreports);

	$data = array();
	$i = 0;

	foreach ($bgbreports as $bgreport) {
		if ($bgreport['brefid'] == $refid || $bgreport['grefid'] == $refid) {
			$data['grefid'] = $bgreport['grefid'];
			$data['brefid'] = $bgreport['brefid'];
			$data['goal'] = $bgreport['goal'];
			$data['objective'] = $bgreport['objective'];
			$data['spr_refid'] = $bgreport['id'];
			$data['period_data'] = $bgreport['period_data'];
			if (isset($bgreport['periods'])) {
				$data['periods'] = json_encode($bgreport['periods']);
			}
		}
	}

	if ($type == 'b') {
		# benchmark
		$graphs = IDEAStudentBenchmarkAssessment::factory($data["brefid"])->getTrialGraph(null);
		$periodsBech = json_decode($data["periods"]);
		$perBechLayout = UILayout::factory();
		foreach ($periodsBech as $key => $value) {
			if ($value->narrative != "") {
				$perBechLayout
					->addHTML('MP-' . $value->bm . ' Comments: ' . $value->narrative)
					->newLine();
			}
		}
		$layout = UILayout::factory()
			->addHTML('', '5%')
			->addHTML($data["objective"])
			->addObject(
				UIAnchor::factory('Documentation')
					->hint('Documentation')
					->css('color', '#0000ff')
					->css('font-weight', 'bold')
					->onClick("callMeasureTests(" . json_encode($data["brefid"]) . ", " . json_encode($dskey) . "); api.event.cancel(event);")
			)
			->newLine()
			->addObject($perBechLayout, '[padding-left: 61px; id=' . $data["brefid"] . '] italic')
			->newLine();
		foreach ($graphs as $trial) {
			$layout
				->newLine()
				->addHTML('')
				->newLine()
				->newLine()
				->addHTML($trial['name'], '[padding-left: 87px]')
				->newLine()
				->addHTML($trial['html'], '[padding-left: 87px]')
				->newLine();
		}
	} elseif ($type == 'g') {
		$periodsGl = json_decode($data['periods']);
		$perGolLayout = UILayout::factory();
		foreach ($periodsGl as $key => $value) {
			if ($value->narrative != "") {
				$perGolLayout
					->addHTML('MP-' . $value->bm . ' Comments: ' . $value->narrative)
					->newLine();
			}
		}
		# goal
		$layout = UILayout::factory()
			->addHTML($data['goal'], '[font-weight: bold;]')
			->newLine()
			->addObject($perGolLayout, 'italic');

	}

	io::ajax('html_cont', $layout->toHTML());
?>

