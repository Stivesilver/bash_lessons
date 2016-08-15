<?php
	Security::init();

	$student_ids = io::get('student_ids');
	$student_ids = explode(',', $student_ids);

	$list = new ListClass();

	$list->setContent(
		IDEAListParts::createListContent('admin', 'std.stdrefid', false, false, (io::get('type') == SCTimeScale::MEDICAID_SERVICE ?
			null :
			" AND EXISTS(SELECT 1 FROM webset.std_srv_rel AS srv WHERE tsrefid = srv.stdrefid AND mp_refid IS NOT NULL)"))
	);
	$list->prepareRow('prepareRow');

	$list->prepareData('getData');

	$list->addRecordsProcess('Process')
		->onClick('processSelected();');
		//->leftIcon('upload.png');

	//$list->editURL = 'javascript: api.window.dispatchEvent(ObjectEvent.COMPLETE, {"student_id": "AF_COL0", "student_name": "AF_COL15"});';

	function prepareRow(ListClassRow $row) {
		global $student_ids;

		if (in_array($row->dataID, $student_ids))
			$row->selectedCheckBox(true);
	}

	function getData($data) {
		$data_std = array();
		foreach ($data as $key => $val) {
			$data_std[$data[$key]['stdrefid']] = $data[$key]['stdlnm'] . ', ' . $val['stdfnm'];
		}
		io::jsVar('data_std', $data_std);
	}

	$list->printList();

?>
<script>
	function processSelected() {
		var sel = ListClass.get().getSelectedValues().values;
		var student_ids = [];
		var i = 0;
		while(i < sel.length) {
			student_ids[i] = data_std[sel[i]];
			i++;
		}
		student_ids = student_ids.join('; ');
		api.window.dispatchEvent(ObjectEvent.COMPLETE, {"student_id": sel, "student_name": student_ids});
		api.window.destroy();
	}
</script>