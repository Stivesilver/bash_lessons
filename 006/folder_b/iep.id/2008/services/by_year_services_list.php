<?php

	Security::init();

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefId = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'Services';

	$list->SQL = "
  		SELECT webset.std_srv_sped.ssmrefid,
               order_num,
               stsdesc,
               sfdesc,
               CASE WHEN round(hours) = hours THEN round(hours) else round(hours,1) END,
               minutes,
               COALESCE(webset.disdef_location.crtdesc, '') || CASE WHEN crtnarrsw = 'Y' THEN ': ' || COALESCE(ssmclasstypenarr, '') ELSE '' END as location,
               ssmteacherother,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sped
	           INNER JOIN webset.statedef_services_sped ON webset.std_srv_sped.stsrefid = webset.statedef_services_sped.stsrefid
	           INNER JOIN webset.disdef_frequency ON webset.std_srv_sped.ssmfreq = webset.disdef_frequency.sfrefid
	           INNER JOIN webset.disdef_location ON webset.std_srv_sped.ssmclasstype = webset.disdef_location.crtrefid
          	   LEFT OUTER JOIN public.sys_usermst ON webset.std_srv_sped.umrefid = public.sys_usermst.umrefid
         WHERE stdrefid = " . $tsRefId . "
           AND iepyear = " . $refID . "
         ORDER BY order_num, ssmrefid
	";

	$list->addColumn("Order #");
	$list->addColumn("Service");
	$list->addColumn("Frequency")->dataCallback('srvList');
	$list->addColumn("Hours")->dataCallback('srvList');
	$list->addColumn("Minutes")->dataCallback('srvList');
	$list->addColumn("Location")->dataCallback('srvList');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyGoals('$dskey')");

	$list->printList();

	function srvList($data, $col) {
		if ($data['nasw'] == 'Y') {
			$return = '';
		} else {
			$return = $data[$col];
		}

		return $return;
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
