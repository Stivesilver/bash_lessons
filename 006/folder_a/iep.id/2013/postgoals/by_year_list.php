<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_SEC_ASSESSMENT_SUMMARY;

	$list = new listClass();

	$list->title = 'Assessments';

	$list->SQL = "
		SELECT std.refid,
			   order_num,
			   COALESCE(txt01, validvalue),
			   dat01,
			   txt02
		  FROM webset.std_general std
		       LEFT OUTER JOIN webset.disdef_validvalues subj ON subj.refid = std.int01
		 WHERE iepyear = " . $refID . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, std.refid
	";

	$list->addColumn('Order #');
	$list->addColumn('Transition Assessment Tool');
	$list->addColumn('Date', '', 'date');
	$list->addColumn('Summary of Results');

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
			api.url('./copy_assessment.ajax.php', {dskey: dskey}),
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
