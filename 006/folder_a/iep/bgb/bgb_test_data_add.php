<?php

	Security::init();

	$test_id = io::geti('test_id');

	$RefID = io::geti('RefID');

	$rows = db::execSQL("
		SELECT mrrefid, mr.name
		  FROM webset.disdef_bgb_measure_rows AS mr
		       LEFT JOIN webset.disdef_bgb_measure_items AS it ON (mr.temp_id = it.mirefid)
		       LEFT JOIN webset.std_bgb_measure_test AS mt ON (it.mirefid = mt.templ_id)
		 WHERE mt.mtrefid = $test_id
	")->assocAll();

	if ($RefID == 0) {
		$defDate = db::execSQL("
			SELECT mdate
			  FROM webset.std_bgb_measure_data
			WHERE test_id = $test_id
			ORDER BY mdrefid DESC
		")->getOne();
	} else {
		$defDate = '';
	}

	if ($RefID != 0) {
		$recData = json_decode(db::execSQL("
			SELECT mdata
			  FROM webset.std_bgb_measure_data
			WHERE mdrefid = $RefID
		")->getOne());
	}

	$edit = new editClass('edit1', $RefID);

	$edit->title = 'Add/Edit Trial';

	$edit->setSourceTable('webset.std_bgb_measure_data', 'mdrefid');

	$edit->addControl('', 'protected')
		->value(db::execSQL("
			SELECT it.description AS desc
              FROM webset.disdef_bgb_measure_items AS it
				   LEFT JOIN webset.std_bgb_measure_test AS mt ON (it.mirefid = mt.templ_id)
			 WHERE mt.mtrefid = $test_id
		")->getOne());


	$edit->addGroup('General Information');
	$edit->addControl('Record Date', 'date')
		->sqlField('mdate')
		->name('mdate')
		->req()
		->value($defDate);

	$edit->addControl('Record Date', 'int')
		->caption('Data Collection Point')
		->sqlField('percent_tag')
		->req();

	$edit->addControl('', 'hidden')
		->value($test_id)
		->sqlField('test_id');

	$i = 1;
	foreach ($rows as $row) {
		$edit->addControl('id', 'hidden')
			->value($row['mrrefid'])
			->name('id-' . $i);
		if (isset($recData->$row['mrrefid'])) {
			$value = $recData->$row['mrrefid'];
		} else {
			$value = '';
		}
		$edit->addControl($row['name'], 'text')
			->width(350)
			->name('row-' . $i)
			->value($value);
		$i++;
	}

	$edit->setPostsaveCallback('saveData', 'bgb_save_record_data.inc.php');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->saveAndEdit = true;

	$edit->topButtons = true;

	$edit->finishURL = CoreUtils::getURL('./bgb_test_data_list.php', array('test_id' => $test_id));
	$edit->cancelURL = CoreUtils::getURL('./bgb_test_data_list.php', array('test_id' => $test_id));

	$edit->printEdit();

?>
