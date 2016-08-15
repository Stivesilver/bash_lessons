<?php

	Security::init();

	$dskey = io::get('dskey');
	$outcome = io::geti('outcome', true);

	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_EC_GOALS;

	$list = new listClass();

	$list->title = 'Annual Goals';

	$list->setMasterRecordID($outcome);

	$list->SQL = "
		SELECT std.refid,
		       order_num,
			   txt06,
			   txt01,
			   txt02,
			   'Objectives: ' || (SELECT count(1)
			      FROM webset.std_general obj
				 WHERE obj.int01 = std.refid AND obj.area_id = 154)
		  FROM webset.std_general std
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area_id . "
		   AND int01 = " . $outcome . "
		 ORDER BY order_num, 3
	";

	$list->addColumn('Order #');
	$list->addColumn('Baseline Performance');
	$list->addColumn('Content Standard(s)');
	$list->addColumn('Annual Goal');
	$list->addColumn('Objectives')
		->type('link')
        ->param('javascript:objectives(AF_REFID);');

	$list->addURL = CoreUtils::getURL('ec_goals_add.php', array('dskey' => $dskey, 'outcome' => $outcome));
    $list->editURL = CoreUtils::getURL('ec_goals_add.php', array('dskey' => $dskey, 'outcome' => $outcome));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$list->addRecordsResequence(
		'webset.std_general',
		'order_num'
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

	print FFInput::factory()->name('dskey')->value($dskey)->hide()->toHTML();
?>
<script type="text/javascript">
    function buildDoc(id) {
        url = api.url('ec_build.ajax.php', {'id': id, 'dskey': $('#dskey').val()});
		//api.window.open('test', url);
        win = api.ajax.process(ProcessType.REPORT, url);
    }

    function objectives(id) {
        url = api.url('ec_objectives.php', {'goal': id, 'dskey': $('#dskey').val()});
		api.window.open('Objectives', url)
			.addEventListener(
				WindowEvent.CLOSE,
				function(e) {
					ListClass.get().reload();
				}
			);
    }
</script>
