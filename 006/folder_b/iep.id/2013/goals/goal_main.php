<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_SEC_GOALS;

	$list = new listClass();

	$list->title = 'Annual Goals';

	$list->SQL = "
		SELECT std.refid,
		       order_num,
		       gdsksdesc as area,
			   txt01,
			   txt03,
			   'Objectives: ' || (SELECT count(1)
			      FROM webset.std_general obj
				 WHERE obj.int01 = std.refid AND obj.area_id = 156)
		  FROM webset.std_general std
			   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON int01 = ksa.gdskrefid
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, 3
	";

	$list->addColumn('Order #');
	$list->addColumn('Skill Area');
	$list->addColumn('Present Level of Performance');
	$list->addColumn('Annual Goal');
	$list->addColumn('Objectives')
		->type('link')
		->param('javascript:objectives(AF_REFID);');

	$list->addURL = CoreUtils::getURL('goal_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('goal_add.php', array('dskey' => $dskey));

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
	//			->setNesting('webset.std_general AS t2', 'refid', 'int01', 'webset.std_general', 'int01')
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$button = new IDEAPopulateIEPYear($dskey, $area_id, '/apps/idea/iep.id/2013/goals/by_year_list.php');
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);



	$list->printList();

	print io::jsVar('dskey', $dskey);

?>
<script type="text/javascript">
	function objectives(id) {
		var url = api.url('objectives.php', {'goal': id, 'dskey': dskey});
		var win = api.window.open('Objectives', url);
		win.addEventListener(
			WindowEvent.CLOSE,
			function () {
				ListClass.get().reload();
			}
		);
	}
//	function populate(dskey) {
//		api.desktop.open(
//				'Populate Anual Goals / ',
//				api.url(api.virtualRoot + '/apps/idea/iep.id/2013/goals/populate.php', {'dskey': dskey})
//			).addEventListener(
//			ObjectEvent.COMPLETE,
//			function (e) {
//				api.reload();
//			}
//		);
//	}
</script>
