<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'Supplementary Aids and Services';

	$list->SQL = "
        SELECT ssmrefid,
               COALESCE(narrative, stsdesc),
			   ssmteacherother,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sup std
               INNER JOIN webset.statedef_services_sup state ON std.stsrefid = state.stsrefid
               LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
         WHERE std.stdrefid=" . $tsRefID . "
		   AND iepyear = ". $refID ."
         ORDER BY 2
	";

	$list->addColumn('Service');
	$list->addColumn('Position Responsible')->dataCallback('clearNAservice');
	$list->addColumn('Start Date')->type('date')->dataCallback('clearNAservice');
	$list->addColumn('Duration')->type('date')->dataCallback('clearNAservice');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyGoals('$dskey')");

	$list->printList();

	function clearNAservice($data, $col) {
		if ($data['nasw'] == 'Y') {
			return '';
		} else {
			return $data[$col];
		}
	}
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
			api.url('./copy_suplementary.ajax.php', {dskey: dskey}),
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
