<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student_name = ucfirst(strtolower(IDEAStudent::factory($tsRefID)->get('stdfirstname')));
	$area_id = IDEAAppArea::ID_SEC_POST_GOALS;

	$list = new listClass();

	$list->title = 'Postgoals';

	$list->SQL = "
		SELECT std.refid,
				   order_num,
				   area.validvalue,
				   dsydesc,
				   REPLACE(stm.validvalue, 'student', '" . db::escape($student_name) . "'),
				   txt01
			  FROM webset.std_general std
			  	   LEFT OUTER JOIN webset.glb_validvalues area ON area.refid = std.int01
			  	   LEFT OUTER JOIN webset.glb_validvalues stm ON stm.refid = std.int02
			  	   LEFT OUTER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = std.int03
			 WHERE iepyear = " . $refID . "
			   AND area_id = " . $area_id . "
			 ORDER BY order_num, std.refid
	";

	$list->addColumn('Order #');
	$list->addColumn('Area');
	$list->addColumn('School Year');

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
