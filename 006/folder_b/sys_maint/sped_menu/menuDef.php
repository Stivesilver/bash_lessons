<?PHP
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->title = "Menu Definitions";

	$list->SQL = "
		SELECT mdrefid,
		       '<img src=" . SystemCore::$virtualRoot . "' || mdicon || '>' AS img,
               mdname,
               mdmenutext,
               mdlink,
               (SELECT count(1)
                  FROM webset.sped_menu mm
                       INNER JOIN webset.sped_menu_set s ON s.srefid = mm.set_refid
                 WHERE (enddate IS NULL or now()< enddate)
                   AND mm.mdrefid=md.mdrefid) AS items
          FROM webset.sped_menudef md
         WHERE (1=1) ADD_SEARCH
         ORDER BY mdname
    ";

	$list->addSearchField("ID", "(mdrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Title")->sqlField("lower(mdname)  like '%' || lower(ADD_VALUE) || '%'");
	$list->addSearchField("Item")->sqlField("lower(mdmenutext)  like '%' || lower(ADD_VALUE) || '%'");
	$list->addSearchField("Link")->sqlField("lower(mdlink)  like '%' || lower(ADD_VALUE) || '%'");
	$list->addSearchField(FFSelect::factory("IEP Format"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY state, shortdesc
	    ")
		->sqlField('EXISTS (SELECT 1 FROM webset.sped_menu mm WHERE md.mdrefid = mm.mdrefid AND set_refid = ADD_VALUE)');

	$list->addSearchField(FFSelect::factory("Application"))
		->sql("
			SELECT scr_refid,
				   scr_name
			  FROM webset.sped_screen
			 ORDER BY scr_refid
	    ")
		->sqlField("
			EXISTS (
					SELECT 1
					  FROM webset.sped_menu mm
					 WHERE md.mdrefid = mm.mdrefid
					   AND mitemapp = ADD_VALUE
				   )
		");

	$list->addColumn('ID')->sqlField('mdrefid');
	$list->addColumn("Icon")->sqlField('img');
	$list->addColumn("Title")->sqlField('mdname');
	$list->addColumn("Item")->sqlField('mdmenutext');
	$list->addColumn("Link")->sqlField('mdlink');
	$list->addColumn("Items")->sqlField('items')->dataCallback('itemstext');

	$list->deleteTableName = "webset.sped_menu";
	$list->deleteKeyField = "mdrefid";

	$list->addURL = CoreUtils::getURL('./menuDefAdd.php');
	$list->editURL = CoreUtils::getURL('./menuDefAdd.php');

	function itemstext($data) {
		$names = db::execSQL("
			SELECT mrefid,
	               shortdesc
		      FROM webset.sped_menu MM
	               INNER JOIN webset.sped_menu_set mset ON mset.srefid = MM.set_refid
		     WHERE MM.mdrefid = " . $data['mdrefid'] . "
	           AND (enddate IS NULL OR now()< enddate)
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['shortdesc'] . "<br/>";
		}
		return $res;
	}

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_menudef')
			->setKeyField('mdrefid')
			->setNesting('webset.sped_menu', 'mdrefid', 'mdrefid', 'webset.sped_menudef', 'mdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
