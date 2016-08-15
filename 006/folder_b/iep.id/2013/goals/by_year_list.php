<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_SEC_GOALS;

	$list = new listClass();

	$list->title = 'Annual Goals';

	$list->SQL = "
		SELECT std.refid,
		       order_num,
		       gdsksdesc as area,
			   txt01,
			   txt03
		  FROM webset.std_general std
			   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON int01 = ksa.gdskrefid
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $refID . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, 3
	";

	$list->addColumn('Order #');
	$list->addColumn('Skill Area');
	$list->addColumn('Annual Goal');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyGoals('$dskey')");

	$list->printList();
?>

<script type="text/javascript">
	function copyGoals(dskey) {
		var refid = ListClass.get().getSelectedValues().values;
		if (refid.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./copy_goals.ajax.php', {dskey: dskey}),
			{RefID: refid.join(',')},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.window.dispatchEvent(ObjectEvent.COMPLETE);
				api.window.destroy();
			}
		)
	}
</script>
