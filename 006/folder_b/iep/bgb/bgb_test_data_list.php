<?php

	Security::init();

	$test_id = io::geti('test_id');

	$rows = db::execSQL("
		SELECT mrrefid, mr.name
		  FROM webset.disdef_bgb_measure_rows AS mr
		       LEFT JOIN webset.disdef_bgb_measure_items AS it ON (mr.temp_id = it.mirefid)
		       LEFT JOIN webset.std_bgb_measure_test AS mt ON (it.mirefid = mt.templ_id)
		 WHERE mt.mtrefid = $test_id
	")->assocAll();

	$edit = new EditClass('edit1', $test_id);

	$edit->SQL = "
			SELECT it.description AS desc
              FROM webset.disdef_bgb_measure_items AS it
				   LEFT JOIN webset.std_bgb_measure_test AS mt ON (it.mirefid = mt.templ_id)
			 WHERE mt.mtrefid = $test_id
	";

	$edit->addControl('Template Description', 'protected')
		->sqlField('desc');

	$edit->firstCellWidth = '12%';
	$edit->printEdit();

	$list = new ListClass();

	$list->getPrinter()->setPageFormat(RCPageFormat::LANDSCAPE);

	$list->title = 'Trials';

	$list->SQL = "
		SELECT mdrefid,
			   mdate,
			   mname,
			   mdata,
			   other,
			   percent_tag,
			   mt.max_points
		  FROM webset.std_bgb_measure_data AS md
		  	   LEFT JOIN webset.std_bgb_measure_test AS mt ON (mt.mtrefid = md.test_id)
		  	   LEFT JOIN webset.disdef_bgb_measure_items AS it ON (it.mirefid = mt.templ_id)
		 WHERE mt.mtrefid = $test_id
		  ORDER BY mdate ASC
    ";

	$list->addColumn('Date')->sqlField('mdate')->type('date');

	$list->addColumn('Data Collection Point')->sqlField('percent_tag')->dataCallback('pointText');

	if (count($rows) > 7) {
		$count = 7;
	} else {
		$count = count($rows);
	}
	for($i = 0; $i < $count; $i++) {
		$list->addColumn(substr($rows[$i]['name'], 0, 7))->sqlField('mdata')
			->dataCallback(create_function('$data', 'return recData($data, ' . $rows[$i]['mrrefid'] . ');'));
	}

	$list->addURL = CoreUtils::getURL('./bgb_test_data_add.php', array('test_id' => $test_id));
	$list->editURL = CoreUtils::getURL('./bgb_test_data_add.php', array('test_id' => $test_id));

	$list->deleteTableName = "webset.std_bgb_measure_data";
	$list->deleteKeyField = "mdrefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_bgb_measure_data')
			->setKeyField('mdrefid')
			->applyListClassMode()
	);

	$list->printList();

	function recData($data, $id) {
		$values = json_decode($data['mdata']);
		return $values->$id;
	}

	function pointText($data) {
		return $data['percent_tag'] . ' of ' . $data['max_points'];
	}

?>

<script type="text/javascript">
		api.window.dispatchEvent("complete");
</script>
