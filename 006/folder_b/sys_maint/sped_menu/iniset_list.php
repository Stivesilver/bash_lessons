<?php

	Security::init();

	$list = new listClass();

	$list->showSearchFields = "yes";

	$list->title = "Menu Set Option Values";

	$list->SQL = "
		SELECT isrefid,
               ini.irefid AS iirefid,
               ini_name,
               '<b>' || ini_codeword || '</b>' AS codeword,
               value
		  FROM webset.sped_ini_set set
               INNER JOIN webset.sped_ini ini ON set.irefid = ini.irefid
         WHERE srefid = " . io::get("iepformat") . "
         ORDER BY ini.irefid
    ";

	$list->addSearchField("ID", "(isrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
    $list->addSearchField('Option', "LOWER(ini_name)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Code Word', "LOWER(ini_codeword)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Entered Value', "LOWER(value)  like '%' || LOWER('ADD_VALUE') || '%'");

	$list->addColumn('ID')->sqlField('isrefid');
	$list->addColumn("Option")->sqlField('ini_name');
	$list->addColumn("Code word")->sqlField('codeword');
	$list->addColumn("Entered Value")->sqlField('value');

	$list->addURL = CoreUtils::getURL('./iniset_edit.php', array('iepformat' => io::get("iepformat")));
	$list->editURL = CoreUtils::getURL('./iniset_edit.php', array('iepformat' => io::get("iepformat")));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_ini_set')
			->setKeyField('isrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
<script language="javascript" src="<?= SystemCore::$virtualRoot; ?>/applications/webset/includes/autoText.js"></script>
