<?php

	Security::init();

	$benchmark_id = io::geti('benchmark_id');

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tsRefID = $ds->safeGet('tsRefID');
	$year = io::geti('year');
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$vourefid = $ds->safeGet('vourefid');

	io::jsVar('dskey', $dskey);
	io::jsVar('siymrefid', $siymrefid);
	io::jsVar('tsRefID', $tsRefID);

	$edit = new EditClass('edit1', $benchmark_id);

	$edit->title = 'Goal / Benchmark';
	$edit->SQL = "
		SELECT COALESCE(goal.overridetext, goal.gsentance) AS goal_text,
			   COALESCE(bench.overridetext, bench.bsentance) AS bench_text
		  FROM webset.std_bgb_goal AS goal
			   LEFT JOIN webset.std_bgb_benchmark AS bench ON bench.grefid = goal.grefid
		 WHERE brefid = $benchmark_id
	";

	$edit->addControl('Goal', 'protected')
		->sqlField('goal_text');

	$edit->addControl('Benchmark', 'protected')
		->sqlField('bench_text');

	$edit->firstCellWidth = '12%';
	$edit->printEdit();

	$list = new ListClass();

	$list->title = 'Benchmark Measurement';

	$list->SQL = "
        SELECT mtrefid,
        	   brefid,
			   COALESCE(bch.overridetext, bch.bsentance) AS bench,
			   it.name AS templ,
			   ct.name AS catname,
			   mt.name,
			   mt.max_points,
			   		   'Trials: ' || (SELECT count(1)
			      				   FROM webset.std_bgb_measure_data
				                  WHERE mt.mtrefid = test_id) AS amount
          FROM webset.std_bgb_measure_test AS mt
          	   LEFT JOIN webset.std_bgb_benchmark AS bch ON (mt.bench_id = bch.brefid)
          	   LEFT JOIN webset.disdef_bgb_measure_items AS it ON (it.mirefid = mt.templ_id)
          	   LEFT JOIN webset.disdef_bgb_measure_cat AS ct ON (ct.mcrefid = it.cat_id)
         WHERE mt.bench_id = $benchmark_id
         ORDER BY ct.order_num, ct.name, it.order_num, it.name
    ";

	$list->addColumn('Category')->sqlField('catname');
	$list->addColumn('Template')->sqlField('templ');
	$list->addColumn('Graph Title')->sqlField('name');
	$list->addColumn('Max Value')->sqlField('max_points');
	$list->addColumn('Trials')
		->sqlField('amount')
		->type('link')
        ->param('javascript:testList(AF_REFID);');

	$list->editURL = 'javascript: testList(AF_REFID)';

	$list->deleteTableName = "webset.std_bgb_measure_test";
	$list->deleteKeyField = "mtrefid";

	$list->addButton(FFIDEAExportButton::factory())
		->setTable('webset.std_bgb_measure_test')
		->setKeyField('mtrefid')
		->applyListClassMode();

	$list->addButton(FFButton::factory('Close'))
		->width(78)
		->onClick('wclose();');

	$temldis = true;
	if (SystemCore::$isAdmin) {
		$temldis = false;
	}
	$list->addButton('Templates', 'addTemplate()')
		->width(78)
		->disabled($temldis);

	$list->addURL = CoreUtils::getURL('bgb_measure_test_add.php', array('dskey' => $dskey, 'benchmark_id' => $benchmark_id));
	$list->editURL = CoreUtils::getURL('bgb_measure_test_add.php', array('dskey' => $dskey, 'benchmark_id' => $benchmark_id));

	$list->addButton(FFMenuButton::factory('Print'))
		->leftIcon('./img/printer.png')
		->addItem(' PDF (Core Version)', 'buildIEP()', './img/PDF.png');

	$list->printList();

?>

<script>

	function wclose() {
		api.window.destroy();
	}

	function testList(refid) {
		var win = api.window.open('Edit Form',
			api.url('./bgb_test_data_list.php'),
			{
				'test_id': refid
			}
		);
		win.resize(900, 700);
		win.show();
		win.addEventListener(WindowEvent.CLOSE, function () {
			api.reload();
		});
	}

	function addTemplate() {
		var win = api.window.open('Edit Form',
			api.url(api.virtualRoot + '/apps/idea/dis_def/mo/tmpl_list.php')
		);
		win.resize(900, 700);
		win.show();
		win.addEventListener(WindowEvent.CLOSE, function () {
			api.reload();
		});
	}

	function buildIEP() {
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('progress_print.ajax.php'),
			{
				'esy': 'N',
				'tsRefID': tsRefID,
				'dskey': dskey,
				'siymrefid': siymrefid
			}
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {

			}
		);
	}

</script>
