<?php

	Security::init();

	$RefID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = '';

	$list->SQL = "
       SELECT redrefid,
              screen.scrdesc,
              red_desc,
              red_text,
              red.lastuser,
              red.lastupdate
         FROM webset.es_std_red AS red
              INNER JOIN webset.es_statedef_screeningtype AS screen ON red.screening_id = screen.scrrefid
        WHERE stdrefid = " . $tsRefID . "
          AND evalproc_id = $RefID
        ORDER BY screen.scrseq, red.redrefid
    ";

	$list->addColumn('Area');
	$list->addColumn('Description Of Data Reviewed');
	$list->addColumn('Summary Of Information Gained');
	$list->addColumn('Last User');
	$list->addColumn('Last Update')->type('date');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyEntries('$dskey', '$RefID')")->width('80px');

	$list->printList();
?>

<script type="text/javascript">

	function copyEntries(dskey, eprefid) {
		var refid = ListClass.get().getSelectedValues().values;
		if (refid.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('proc_summary_list_copy.php', {dskey: dskey, 'eprefid' : eprefid}),
			{refids: refid.join(',')},
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
