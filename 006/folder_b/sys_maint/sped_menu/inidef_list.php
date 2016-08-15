<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->title = "Initialisation Options";

	$list->SQL = "
		SELECT irefid,
               '<u>' || ini_name || '</u>' AS ininame,
               '<b>' || ini_codeword || '</b>' AS cword,
               ini_desc
		  FROM webset.sped_ini ini
         WHERE (1=1) ADD_SEARCH
         ORDER BY irefid
    ";

	$list->addSearchField("ID", "(irefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('Name', "LOWER(ini_name)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Code Word', "LOWER(ini_codeword)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Default', "LOWER(ini_default)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(FFSelect::factory("IEP Format"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY state, shortdesc
		")
		->sqlField("EXISTS (SELECT 1 FROM webset.sped_ini_set iset WHERE ini.irefid = iset.irefid AND srefid = ADD_VALUE)");

	$list->addColumn('ID')->sqlField('irefid');
	$list->addColumn("Name")->sqlField('ininame')->dataHintCallback('namehint');;
	$list->addColumn("Code Word")->sqlField('cword');
	$list->addColumn("Descrpition")->sqlField('ini_desc');

	function namehint($data) {
		$names = db::execSQL("
		SELECT shortdesc || ' | ' || COALESCE( value, '') AS desc
                   FROM webset.sped_menu_set set
                        LEFT OUTER JOIN webset.sped_ini_set ini ON set.srefid = ini.srefid AND irefid  = " . $data['irefid'] . "
                  WHERE (enddate IS NULL OR now()< enddate)
                  ORDER BY state, shortdesc
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['desc'] . "<br/>";
		}
		return $res;
	}

	$list->addURL = CoreUtils::getURL('./inidef_edit.php');
	$list->editURL = CoreUtils::getURL('./inidef_edit.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_ini')
			->setKeyField('irefid')
			->setNesting('webset.sped_ini_set', 'irefid', 'irefid', 'webset.sped_ini', 'irefid')
			->applyListClassMode()
	);

	$list->printList();
?>
