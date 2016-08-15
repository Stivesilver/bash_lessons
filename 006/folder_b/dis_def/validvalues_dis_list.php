<?php

	Security::init();

	$list = new listClass();
	$list->printable = true;

	$list->title = (io::get('title') ? io::get('title') : 'Valid Values');

	$list->showSearchFields = true;

	$list->SQL = "
			SELECT refid,
			       validvalueid,
			       validvalue,
			       CASE
			       WHEN NOW() > glb_enddate THEN 'N'
			       ELSE 'Y'
			       END
			  FROM webset.disdef_validvalues
			 WHERE valuename = '" . io::get('area') . "'
			   AND vndrefid = VNDREFID
			   ADD_SEARCH
			 ORDER BY sequence_number, valuename, validvalue ASC
	";

	$list->addSearchField('Value Code', "LOWER(validvalueid)  like '%' || LOWER('ADD_VALUE') || '%'")->width(50);
	$list->addSearchField('Value Text', "LOWER(validvalue)  like '%' || LOWER('ADD_VALUE') || '%'")->width('50%');
	$list->addSearchField(FFIDEAStatus::factory())
		->sqlField("CASE WHEN NOW() > glb_enddate THEN 'N' ELSE 'Y' END");

	$list->addColumn("Value Code")->width(5);
	$list->addColumn("Value Text");
	$list->addColumn('Active')->type('switch');

	$list->addURL = CoreUtils::getURL(
		"./validvalues_dis_edit.php", 
		array(
			'area' => io::get('area'),
			'title' => io::get('title')
		)
	);
	$list->editURL = $list->addURL;

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.disdef_validvalues')
		->setKeyField('refid')
		->applyListClassMode()
	);

	$list->addRecordsResequence(
		'webset.disdef_validvalues',
		'sequence_number'
	);

	$list->printList();
?>
