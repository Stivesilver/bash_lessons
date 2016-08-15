<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_EC_MAIN;
	$path = io::get('path', true);

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
		   AND iepyear = " . $refID . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, dat01 desc, lastupdate desc
	";

	$list->addColumn('Include in EC Builder')->type('switch');
	$list->addColumn('Order #');
	$list->addColumn('Document date')->type('date');
	$list->addColumn('Outcome');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyGoals('$dskey', '$path')");

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
			api.url('copy_ec.ajax.php', {dskey: dskey}),
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
