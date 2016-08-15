<?php
	Security::init();

	$list = new listClass();

	$list->title = "Constructions Group";

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT cgrefid,
		       cgname,
		       CASE
		       WHEN NOW() > enddate THEN 'N'
		       ELSE 'Y'
		       END AS status
		  FROM webset.sped_constructions_group
		 ORDER BY cgname
	";

	$list->addSearchField("ID", "(cgrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'In-Active')");
    $list->addSearchField('Name', "cgname")->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn("ID")->sqlField('cgrefid');
	$list->addColumn("Group Name")->sqlField('cgname');


	$list->addURL = CoreUtils::getURL('./constructions_group_edit.php');
	$list->editURL = CoreUtils::getURL('./constructions_group_edit.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_constructions_group')
			->setKeyField('cgrefid')
			->setNesting('webset.sped_constructions', 'cnrefid', 'group_id', 'webset.sped_constructions_group', 'cgrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
