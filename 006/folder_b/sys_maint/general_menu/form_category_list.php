<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT mfccatrefid,mfccatdesc
		  FROM webset.def_formcategories
    ";

	$list->title = "Form Category";
	$list->addSearchField("ID", "(mfccatrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");

	$list->addColumn('ID')->sqlField('mfccatrefid');
	$list->addColumn("Form Category")->sqlField('mfccatdesc');

	$list->addURL = CoreUtils::getURL('./form_category_add.php');
	$list->editURL = CoreUtils::getURL('./form_category_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_formcategories')
			->setKeyField('mfccatrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
