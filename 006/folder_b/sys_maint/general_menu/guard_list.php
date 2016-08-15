<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT gtrefid,
               gtdesc,
               gtrank,
               CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END  AS status
          FROM webset.def_guardiantype
         WHERE (1=1) ADD_SEARCH
         ORDER BY gtrank
    ";

	$list->title = "Guardian Type";

	$list->addSearchField("ID", "(gtrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Guardian Type")->sqlField("lower(gtdesc)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addSearchField(FFIDEAStatus::factory())
		->sqlField("CASE enddate<now() WHEN true THEN 'N' ELSE 'Y' END");

	$list->addColumn('ID')->sqlField('gtrefid');
	$list->addColumn("Guardian Type")->sqlField('gtdesc');
	$list->addColumn("Rank")->sqlField('gtrank');
	$list->addColumn("Form Status")->sqlField('status')->type('switch');

	$list->addURL = CoreUtils::getURL('./guard_add.php');
	$list->editURL = CoreUtils::getURL('./guard_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_guardiantype')
			->setKeyField('gtrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
