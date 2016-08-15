<?php
	Security::init();

	$dskey = io::get('dskey', true);
	$dataRows = unserialize(DataStorage::factory($dskey)->get('dataRows'));

	$list = new ListClass();

	$list->printable = true;
	$list->hideCheckBoxes = false;
	$list->pageCount = 2000;

	$list->title = db::execSQL("
		SELECT sql_about
		FROM webset.sys_sql_archive
		WHERE refid = " . io::geti('RefID') . "
		")->getOne();

	$list->fillData($dataRows);


	$list->addSearchField(FFSwitchYN::factory('Valid'), 'valid');
	$list->addSearchField('Details', 'details');
	$list->showSearchFields = true;

	$list->addColumn('Server')
		->sqlField('server')
		->type('group');

	$list->addColumn('Check Method')
		->sqlField('checkmethod');

	$list->addColumn('Check Target')
		->sqlField('parameters');

	$list->addColumn('Valid')
		->sqlField('valid')
		->type('switch');

	$list->addColumn('Details')
		->sqlField('details');

	$list->addButton('Repeat for Selected Servers')
		->onClick('repeatFor()');

	print FFInput::factory()->name('RefID')->value(io::geti('RefID'))->hide()->toHTML();

	print $list->toHTML();
?>
	<script type='text/javascript'>
	function repeatFor() {
		ids = ListClass.get().getSelectedValues().values;
		if (ids == '') {
			api.alert('Please select at least one record');
			return;
		}
		api.window.dispatchEvent('repeat_again', {RefID: $('#RefID').val(), ids: ids});
		api.window.destroy();
	}
	</script>
