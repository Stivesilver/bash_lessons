<?php
	Security::init();

	$list = new listClass();

	$list->title = "Menu Screen Types";

	$list->SQL = "
		SELECT scr_refid,
		       scr_codeword,
		       scr_name,
		       scr_url,
		       scr_desc,
		       scr_default_sw,
		       lastuser,
		       lastupdate
		  FROM webset.sped_screen
		 ORDER BY scr_refid
      ";

	$list->addSearchField("ID", "(scr_refid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addColumn('ID')->sqlField('scr_refid');
	$list->addColumn("Code Word");
	$list->addColumn("Screen Type");
	$list->addColumn("Screen URL");
	$list->addColumn("Screen Description");
	$list->addColumn("Default Screen")->type('switch');
	$list->addColumn("Last User");
	$list->addColumn("Last Update");

	$list->addURL = CoreUtils::getURL('./menu_screen_edit.php');
	$list->editURL = CoreUtils::getURL('./menu_screen_edit.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_screen')
			->setKeyField('scr_refid')
			->applyListClassMode()
	);

	$list->printList();

?>
