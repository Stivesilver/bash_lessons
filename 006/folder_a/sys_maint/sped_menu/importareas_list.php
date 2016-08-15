<?php
	Security::init();

	$list = new listClass();

	$list->title = "Import Areas";

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT imrefid,
               shortdesc,
               imarea,
               seqnum,
               CASE WHEN NOW() > ia.enddate THEN 'N' ELSE 'Y' END  AS status
		  FROM webset.sped_importmst ia
               INNER JOIN webset.sped_menu_set ON setrefid = srefid
         WHERE (1=1) ADD_SEARCH
         ORDER BY state, shortdesc, seqnum, imarea
	";

	$list->addSearchField("ID", "(imrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('Name', "LOWER(imarea)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(FFSelect::factory("IEP Format"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY state, shortdesc
		")
		->sqlField("srefid");
	$list->addSearchField(FFIDEAStatus::factory('Status'))
		->sqlField("(CASE ia.enddate<now() WHEN true THEN 'N' ELSE 'Y' END)");

	$list->addColumn("ID")->sqlField('imrefid');
	$list->addColumn("IEP Format")->sqlField('shortdesc');
	$list->addColumn("Import Area")->sqlField('imarea');
	$list->addColumn("Status")->sqlField('status')->type('switch');
	$list->addColumn("Display Sequence")->sqlField('seqnum');

	$list->addRecordsResequence(
		'webset.sped_importmst',
		'seqnum'
	);

	$list->addURL = CoreUtils::getURL('./importareas_edit.php', array('staterefid' => -1));
	$list->editURL = CoreUtils::getURL('./importareas_edit.php', array('staterefid' => -1));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_importmst')
			->setKeyField('imrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
