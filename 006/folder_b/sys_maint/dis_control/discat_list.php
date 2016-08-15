<?php

	Security::init();

	$list = new ListClass();

	$list->showSearchFields = "yes";

	$list->SQL = "
		SELECT sdcatrefid,
			   name,
			   lastuser,
			   lastupdate
		  FROM webset.statedef_discontrol_cat
		 ORDER BY order_num, name
    ";

	$list->title = "District Control";

	$list->addSearchField("ID", "(sdcatrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Name")->sqlField('name');

	$list->addColumn('ID')->sqlField('sdcatrefid');
	$list->addColumn("Name", "", "text")->sqlField('name');
	$list->addColumn('Lastuser')->sqlField('lastuser');
	$list->addColumn('Lastupdate')->sqlField('lastupdate');


	$list->addURL = "./discat_add.php";
	$list->editURL = "./discat_add.php";

	$list->deleteTableName = "webset.statedef_discontrol_cat";
	$list->deleteKeyField = "sdcatrefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_discontrol_cat')
			->setKeyField('sdcatrefid')
			->setNesting('webset.def_discontrol', 'dcrefid', 'sdcatrefid', 'webset.statedef_discontrol_cat', 'sdcatrefid')
			->setNesting('webset.statedef_discontrol', 'sdcrefid', 'dcrefid', 'webset.def_discontrol', 'dcrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
