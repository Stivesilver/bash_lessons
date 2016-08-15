<?php
	Security::init();

	$list = new listClass();

	$list->title = "Valid Values";

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT refid,
               validvalue,
               validvalueid,
               sequence_number,
               glb_enddate,
               lastuser,
               lastupdate
          FROM webset.glb_validvalues
          WHERE valuename = '" . io::get("area") . "' ADD_SEARCH
         ORDER BY valuename, sequence_number, validvalue ASC
    ";

	$list->addSearchField("Value Text", "", "TEXT")->sqlField('validvalue');
	$list->addSearchField(FFSwitchAI::factory('Status'))->sqlField('(CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END)');

	$list->addColumn("Value Text", "", "TEXT")->sqlField('validvalue');
	$list->addColumn("Value Code", "", "TEXT")->sqlField('validvalueid');
	$list->addColumn("Sequence Number", "", "TEXT")->sqlField('sequence_number');
	$list->addColumn("Expire Date", "", "TEXT")->sqlField('glb_enddate');

	$list->addURL = CoreUtils::getURL('./validvalues_add.php', array('area' => io::get('area')));
	$list->editURL = CoreUtils::getURL('./validvalues_add.php', array('area' => io::get('area')));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.glb_validvalues')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$list->printList();

?>
