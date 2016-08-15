<?php
	Security::init();

	$ds = new DataStorage(io::get('dskey'));

	$condition = $ds->get('condition');

	$list = new ListClass();
	$list->showSearchFields = true;
	$list->hideCheckBoxes = true;

	$list->setContent(
		IDEAListParts::createListContent('admin', 'std.stdrefid', false, false, $condition)
	);

	$list->editURL = 'javascript: selectRow("AF_REFID", "AF_COL16");';

	$list->printList();

	io::js('
		function selectRow(stdrefid, std_name) {
			api.window.dispatchEvent(
				ObjectEvent.SELECT, 
				{"stdrefid" : stdrefid, "std_name" : std_name}
			)
		}
	');

?>
