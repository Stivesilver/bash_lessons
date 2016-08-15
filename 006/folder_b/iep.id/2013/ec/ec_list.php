<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$path = '/apps/idea/iep.id/2013/ec/by_year_list.php';

	$area_id = IDEAAppArea::ID_EC_MAIN;

	$list = new listClass();

	$list->title = 'Outcomes/EC Goals';

	$list->SQL = "
		SELECT std.refid,
			   CASE WHEN int10=1 THEN 'Y' ELSE 'N' END as show_in_builder,
			   order_num,
		       dat01,
		       validvalueid,
			   (SELECT count(1)
			      FROM webset.std_general goals
				 WHERE goals.area_id = " . IDEAAppArea::ID_EC_GOALS . "
		           AND goals.int01 = std.refid) || '/' ||
				(SELECT count(1)
			      FROM webset.std_general goals
				       INNER JOIN webset.std_general obj ON obj.int01 = goals.refid AND obj.area_id = 154
				 WHERE goals.area_id = " . IDEAAppArea::ID_EC_GOALS . "
		           AND goals.int01 = std.refid),
			   std.lastuser,
			   std.lastupdate,
			   'Print'
		  FROM webset.std_general std
			   INNER JOIN webset.glb_validvalues outcome ON int01 = outcome.refid
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, dat01 desc, lastupdate desc
	";

	$list->addColumn('Include in EC Builder')->type('switch');
	$list->addColumn('Order #');
	$list->addColumn('Document date')->type('date');
	$list->addColumn('Outcome');
	$list->addColumn('Goals/Objectives')
		->type('link')
        ->param('javascript:goals(AF_REFID);');
	$list->addColumn('Last User');
	$list->addColumn('Last Update');

	$list->addURL = CoreUtils::getURL('ec_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('ec_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$list->addRecordsResequence(
		'webset.std_general',
		'order_num'
	);

	$button = new IDEAPopulateIEPYear($dskey, $area_id, $path);
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();

	print FFInput::factory()->name('dskey')->value($dskey)->hide()->toHTML();

?>
<script type="text/javascript">
    function goals(id) {
        url = api.url('ec_goals.php', {'outcome': id, 'dskey': $('#dskey').val()});
		api.window.open('Goals', url)
			.addEventListener(
				WindowEvent.CLOSE,
				function(e) {
					ListClass.get().reload();
				}
			);
    }
</script>
