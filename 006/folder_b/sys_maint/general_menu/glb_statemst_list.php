<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT staterefid, state, statename
		  FROM webset.glb_statemst
		 WHERE (1=1) ADD_SEARCH
         ORDER BY state
";

	$list->title = "States Codes";

	$list->addSearchField("State ID", "(staterefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Code")->sqlField('state')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField("State")->sqlField('statename')->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn("State ID #")->sqlField('staterefid')->width('5%');
	$list->addColumn("State Code")->sqlField('state')->width('5%');
	$list->addColumn("State Name")->sqlField('statename');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.glb_statemst')
			->setKeyField('staterefid')
			->applyListClassMode()
	);

	$list->printList();
?>
