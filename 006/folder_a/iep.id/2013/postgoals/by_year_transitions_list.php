<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student_name = ucfirst(strtolower(IDEAStudent::factory($tsRefID)->get('stdfirstname')));
	$area_id = IDEAAppArea::ID_SEC_TRANS_ACTIVITIES;

	$list = new listClass();

	$list->title = 'Transition Activities';

	$list->SQL = "
		SELECT std.refid,
		       order_num,
			   area.validvalueid || '. ' || area.validvalue,
			   dsydesc,
			   txt01,
			   txt02,
			   dat01,
			   status.sequence_number || COALESCE(' ' || txt03, '') as status,
			   dat02
		  FROM webset.std_general std
			   INNER JOIN webset.glb_validvalues area ON area.refid = std.int01
			   INNER JOIN webset.glb_validvalues status ON status.refid = std.int02
			   LEFT OUTER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = std.int03
		 WHERE iepyear = " . $refID . "
		   AND area_id = " . $area_id . "
		 ORDER BY COALESCE(order_num), area.sequence_number, dat01, std.refid
	";

	$list->addColumn('Order #');
	$list->addColumn('Transition Activities');
	$list->addColumn('School Year');
	$list->addColumn('Description');

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
			api.url('./copy_postgoals.ajax.php', {dskey: dskey}),
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
