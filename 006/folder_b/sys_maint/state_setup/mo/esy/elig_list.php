<?php
	Security::init();

	$list = new listClass();

	$list->multipleEdit = true;
	$list->showSearchFields = true;

	$list->title = "Eligibilty for ESY services";

	$list->SQL = "
		SELECT refid, validvalueid, validvalue
          FROM webset.glb_validvalues
         WHERE valuename = 'MO_ESY_Elig'
         ORDER BY CASE validvalueid = 'W' WHEN TRUE THEN 'Z' ELSE validvalueid END
    ";

	$list->addSearchField("ID", "(refid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addColumn('ID')->sqlField('refid');
	$list->addColumn("Value", "10%", "text")->sqlField('validvalueid');
	$list->addColumn("Description", "90%", "text")->sqlField('validvalue');

	$list->addURL =  CoreUtils::getURL('./elig_add.php');
	$list->editURL =  CoreUtils::getURL('./elig_add.php');

	$list->deleteTableName = "webset.glb_validvalues";
	$list->deleteKeyField = "refid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.glb_validvalues')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$list->printList();
?>
