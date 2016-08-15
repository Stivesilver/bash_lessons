<?php

	Security::init();

	$area = io::get('area');
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$edit = new EditClass("edit1", 0);

	$edit->title = 'Update Services Dates';

	$edit->addGroup('General Information');

	$edit->addControl('Beginning Date', 'date')
		->name('begdate')
		->value($student->getDate('stdenrolldt'))
		->req();

	$edit->addControl('Ending Date', 'date')
		->name('enddate')
		->value($student->getDate('stdcmpltdt'))
		->req();

	$edit->finishURL = 'javascript:postDates(' . json_encode($area) . ', ' . json_encode($dskey) . ')';
	$edit->cancelURL = 'javascript:api.window.destroy()';

	$edit->firstCellWidth = "50%";
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = false;

	$edit->getButton(EditClassButton::SAVE_AND_FINISH)
		->value('Update');

	$edit->printEdit();
?>
<script type="text/javascript">
	function postDates(area, dskey) {
		var url = api.url('srv_update_dates.ajax.php');
		api.ajax.post(
			url,
			{
				'area': area,
				'dskey': dskey,
				'begdate': $('#begdate').val(),
				'enddate': $('#enddate').val()
			},
			function () {
				api.window.dispatchEvent('dates_updated');
				api.window.destroy();
			}
		);
	}
</script>