<?php
	Security::init();

	$list = new listClass();
	//	   IEPFormat

	$iepformat = io::get('IEPFormat');

	if (!$iepformat || $iepformat == -1) {
		$where = " sped_menu_set.srefid = 19";
		$iepformat = 19;
	} else {
		$where = " sped_menu_set.srefid = " . $iepformat;
	}

	$list->title = "Menu Manager";

	$list->SQL = "
			SELECT mrefid,
                   mitemgroup,
                   mdmenutext,
                   mitemorder,
                   mitemnewline,
                   mgroupnewline,
				   mitem_iep_req_sw,
                   displcondition,
				   check_method,
				   check_param
              FROM webset.sped_menu MM
                   LEFT OUTER JOIN webset.sped_menudef MD ON MM.mdrefid=MD.mdrefid
                   INNER JOIN webset.sped_menu_set ON webset.sped_menu_set.srefid = MM.set_refid
             WHERE $where ADD_SEARCH
             ORDER BY shortdesc, mitemgroup, mitemorder
      ";

	$list->showSearchFields = true;

	$list->addSearchField("ID", "(mrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory("IEP Format"))
		->sql("
	        SELECT srefid, shortdesc
	          FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
	         ORDER BY state, shortdesc
        ")->name('IEPFormat')
		->value($iepformat);

	$list->addSearchField(FFSelect::factory("Application"))
		->sql("
		    SELECT scr_refid,
			       scr_name
			  FROM webset.sped_screen
			 ORDER BY scr_refid
        ")->sqlField('mitemapp');

	$list->addSearchField(FFSelect::factory("Group"))
		->sql("
            SELECT DISTINCT mitemgroup, mitemgroup
              FROM webset.sped_menu MM
                   LEFT OUTER JOIN webset.sped_menudef MD ON MM.mdrefid=MD.mdrefid
                   INNER JOIN webset.sped_menu_set ON webset.sped_menu_set.srefid = MM.set_refid
             WHERE sped_menu_set.srefid = VALUE_01
             ORDER BY mitemgroup
        ")->sqlField('mitemgroup')
		->tie('IEPFormat');

	$list->addSearchField('Menu Item', "LOWER(mdmenutext)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Condition File', "LOWER(displcondition)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Check Method', "LOWER(check_method)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Check Parameter', "LOWER(check_param)  like '%' || LOWER('ADD_VALUE') || '%'");

	$list->addColumn('ID')->sqlField('mrefid');
	$list->addColumn("Group", "", "group")->sqlField('mitemgroup');
	$list->addColumn("Name")->sqlField('mdmenutext');
	$list->addColumn("INL")->sqlField('mitemnewline')->type('switch');
	$list->addColumn("GNL")->sqlField('mgroupnewline')->type('switch');
	$list->addColumn("IEP Year Req")->sqlField('mitem_iep_req_sw')->type('switch');
	$list->addColumn("Condition File")->sqlField('displcondition');
	$list->addColumn("Check Method")->sqlField('check_method');
	$list->addColumn("Check Parameter")->sqlField('check_param');
	$list->addColumn("Seq")->sqlField('mitemorder');

	$list->addRecordsResequence(
		'webset.sped_menu',
		'mitemorder'
	);

	$list->addURL = CoreUtils::getURL('./menu_man_edit.php', array('IEPFormat' => $iepformat));
	$list->editURL = CoreUtils::getURL('./menu_man_edit.php', array('IEPFormat' => $iepformat));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_menu')
			->setKeyField('mrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
