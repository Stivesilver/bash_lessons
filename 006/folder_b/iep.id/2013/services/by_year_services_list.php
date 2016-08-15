<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'Special Education Services';

	$list->SQL = "
        SELECT ssmrefid,
			   order_num,
               COALESCE(stsother, stsdesc),
			   ssmteacherother,
			   impl_oth,
			   COALESCE(ssmclasstypenarr, crtdesc),
               bcpdesc,
			   minutes,
			   sfdesc,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sped std
               INNER JOIN webset.statedef_services_sped state ON std.stsrefid = state.stsrefid
               INNER JOIN webset.disdef_location class ON std.ssmclasstype = class.crtrefid
			   INNER JOIN webset.disdef_frequency freq ON std.ssmfreq = freq.sfrefid
         WHERE std.stdrefid = " . $tsRefID . "
		   AND iepyear = ". $refID ."
		 ORDER BY order_num, ssmrefid
	";

	$list->addColumn('Order #');
	$list->addColumn('Service');
	$list->addColumn('Position Responsible')->dataCallback('clearNAservice');
	$list->addColumn('Implementor')->dataCallback('clearNAservice');
	$list->addColumn('Location')->dataCallback('clearNAservice');

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
			api.url('./copy_services.ajax.php', {dskey: dskey}),
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
