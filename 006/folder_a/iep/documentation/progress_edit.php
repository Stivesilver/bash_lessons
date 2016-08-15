<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$bgbreports = $ds->safeGet('bgbreports');
	$tsRefID = $ds->safeGet('tsRefID');
	$siymrefid = io::geti('siymrefid');
	$esy = io::get('esy');
	$id = io::geti('id');
	$type = io::get('type');
	$smpRefid = io::get('smpRefid');
	$where = '';
	$next = null;
	$name = '';
	$ntype = '';

	$i = 0;
	foreach ($bgbreports as $report) {
		if ($i == 1) {
			if ($report['goal'] != '') {
				$next = $report['grefid'];
				$ntype = 'g';
			} else {
				$next = $report['brefid'];
				$ntype = 'b';
			}
		}
		$i = 0;
		if($type == 'g'){
			if($report['grefid'] == $id) {
				$name = $report['goal'];
				$i = 1;
			}
		}
		if($type == 'b'){
			if($report['brefid'] == $id) {
				$name = $report['objective'];
				$i = 1;
			}
		}
	}

	# need for get spr_refid
	if ($type == 'g') {
		$where = "WHERE sbg_grefid = $id";
	} else {
		$where = "WHERE sbb_brefid = $id";
	}

	$row = db::execSQL("
		SELECT *
		  FROM webset.std_progress_reporting
		$where
	")->fields;

	# generate default json if empty col in db
	if ($row['spr_period_data'] == '') {
		$row['spr_period_data'] = '{ "' . $smpRefid . '" : {"narrative":"", "extentProgress": ""}}';
	}

	$extentProgress = json_decode($row['spr_period_data'], true);
	# if not exist data for current period add new key
	if (!isset($extentProgress[$smpRefid])) {
		$extentProgress[$smpRefid] = array('narrative' => '', 'extentProgress' => '');
	}

	$edit = new EditClass('edit1', $row['spr_refid']);

	$edit->title = 'Add/Edit Progress';

	$edit->setPresaveCallback('saveProgRep', 'progrep.inc.php');
	$edit->addGroup('General Information');

	$year = IDEAStudentIEPYear::factory($siymrefid)->getIEPYearPeriod();

	$periodName = db::execSQL("
        SELECT smp_period
          FROM webset.sch_marking_period
		 WHERE smp_refid = $smpRefid
	")->getOne();

	$edit->addControl(FFInput::factory())
		->caption('Period')
		->transparent(true)
		->width('90%')
		->readOnly(true)
		->value($periodName);

	$edit->addControl(FFInput::factory())
		->caption('IEP Year')
		->transparent(true)
		->width('90%')
		->readOnly(true)
		->value($year);

	$edit->addControl(FFInput::factory())
		->caption('Goal/Benchmark Name')
		->transparent(true)
		->width('90%')
		->readOnly(true)
		->value($name);

	$pr_goals = db::execSQL("
		SELECT eprefid,
               epsdesc
          FROM webset.disdef_progressrepext
         WHERE vndrefid = VNDREFID
         ORDER BY epseq, eprefid
	")->keyedCol();

	$edit->addControl('Extent of Progress toward the goal', 'select_radio')
		->name('extentProgress')
		->sql("
			SELECT eprefid,
            	   epsdesc || ' - ' || epldesc
	          FROM webset.disdef_progressrepext
	         WHERE vndrefid = VNDREFID
	         ORDER BY epseq, eprefid
		")
		->value($extentProgress[$smpRefid]['extentProgress'])
		->req(true)
		->breakRow();

	$edit->addControl('Narrative', 'textarea')
		->name('narrative')
		->value($extentProgress[$smpRefid]['narrative'])
		->css('width', '100%')
		->css('height', '50');

	$edit->addControl('', 'hidden')
		->name('spr_refid')
		->value($row['spr_refid']);

	$edit->addControl('', 'hidden')
		->name('smp_refid')
		->value($smpRefid);

	$edit->addControl('', 'hidden')
		->name('spr_period_data')
		->value($row['spr_period_data']);

	$edit->addControl('', 'hidden')
		->name('id')
		->value($id);

	$edit->addControl('', 'hidden')
		->name('type')
		->value($type);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(!empty($row['lastuser']) ? $row['lastuser'] : SystemCore::$userUID);
	$edit->addControl('Last Update', 'protected')->value(!empty($row['lastupdate']) ? $row['lastupdate'] : date('m-d-Y H:i:s'));

	$edit->finishURL = "javascript:sentLabel(); api.window.destroy();";

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_progress_reporting')
			->setKeyField('spr_refid')
			->setRefids($row['spr_refid'])
			->setDsKey(json_encode($dskey))
	);

	$edit->cancelURL = "javascript:api.window.destroy();";

	$edit->saveAndAdd = false;

	if ($next) {
		$edit->addButton(
			FFButton::factory()
				->caption('Save & Add')
				->onClick('goToNext(' . $next . ')')
				->width(113)
		);
	}

	$edit->firstCellWidth = '30%';

	$edit->printEdit();

	io::jsVar('dskey', $dskey);
	io::jsVar('refid', $id);
	io::jsVar('esy', $esy);
	io::jsVar('siymrefid', $siymrefid);
	io::jsVar('tsRefID', $tsRefID);
	io::jsVar('smpRefid', $smpRefid);
	io::jsVar('type', $ntype);
	io::jsVar('ctype', $type);
	io::jsVar('goals', $pr_goals);

?>
<script>
	function goToNext(id) {
		EditClass.get().save();
		EditClass.get().addEventListener(
			ObjectEvent.SAVED,
			function (e) {
				sentLabel();
				e.preventDefault();
				api.goto(
					'progress_edit.php',
					{
						'dskey': dskey,
						'esy': esy,
						'siymrefid': siymrefid,
						'id': id,
						'type': type,
						'smpRefid': smpRefid
					}
				);
			}
		)

	}

	function sentLabel() {
		var val = $('#extentProgress').val();
		var goal = goals[val];
		api.window.dispatchEvent('cm', {period: goal, narrative: $('#narrative').val(), refid: refid, ctype : ctype});
	}
</script>
