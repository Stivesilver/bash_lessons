<?PHP
	Security::init();

	$list = new listClass();

	$list->showSearchFields = "yes";

	$list->title = "IEP Format";

	$list->SQL = "
		SELECT srefid,
               state,
               shortdesc,
               'IEP Blocks' AS iepbl,
               'Doc Types' AS doctype,
               'Options' AS iep_options,
               def_sw,
               CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END AS status
          FROM webset.sped_menu_set
         WHERE 1=1 ADD_SEARCH
         ORDER BY state, shortdesc
    ";

	$list->addSearchField("ID", "(srefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'Inactive')")
		->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
		->value(1);

	$list->addColumn('ID')->sqlField('srefid');
	$list->addColumn("State")->sqlField('state');
	$list->addColumn("Menut State Set")->sqlField('shortdesc');
	$list->addColumn("IEP Blocks")->dataCallback('iepblocks')->sqlField('iepbl')->dataHintCallback('iepblockshint');
	$list->addColumn("Doc Types")->dataCallback('doctypes')->sqlField('doctype')->dataHintCallback('doctypeshint');
	$list->addColumn("Options")->dataCallback('options')->sqlField('iep_options')->dataHintCallback('optionsshint');
	$list->addColumn("Default")->sqlField('def_sw')->type('switch');
	$list->addColumn("Status")->sqlField('status')->type('switch');

	$list->addURL = CoreUtils::getURL('./menuSetAdd.php');
	$list->editURL = CoreUtils::getURL('./menuSetAdd.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_menu_set')
			->setKeyField('srefid')
			->setNesting('webset.sped_iepblocks', 'ieprefid', 'iepformat', 'webset.sped_menu_set', 'srefid')
			->setNesting('webset.sped_doctype', 'drefid', 'setrefid', 'webset.sped_menu_set', 'srefid')
			->setNesting('webset.sped_ini_set', 'isrefid', 'srefid', 'webset.sped_menu_set', 'srefid')
			->applyListClassMode()
	);

	$list->printList();

	function iepblocks($data) {
		$count = db::execSQL("
			SELECT count(ieprefid)
              FROM webset.sped_iepblocks
             WHERE iepformat = " . $data['srefid'] . "
		")->getOne();
		return UIAnchor::factory("IEP Blocks (" . $count . ")")->onClick('iepblocks(AF_REFID, event)')->toHTML();
	}

	function iepblockshint($data) {
		$names = db::execSQL("
			SELECT iepdesc
              FROM webset.sped_iepblocks
             WHERE iepformat = " . $data['srefid'] . "
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['iepdesc'] . "<br/>";
		}
		return $res;
	}

	function doctypes($data) {
		$count = db::execSQL("
			SELECT count(drefid)
			  FROM webset.sped_doctype
             WHERE setrefid = " . $data['srefid'] . "
		")->getOne();
		return UIAnchor::factory("Doc Types (" . $count . ")")->onClick('doctypes(AF_REFID, event)')->toHTML();
	}

	function doctypeshint($data) {
		$names = db::execSQL("
			SELECT doctype
			  FROM webset.sped_doctype
             WHERE setrefid = " . $data['srefid'] . "
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['doctype'] . "<br/>";
		}
		return $res;
	}

	function options($data) {
		$count = db::execSQL("
			SELECT count(isrefid)
			  FROM webset.sped_ini_set
			 WHERE srefid = " . $data['srefid'] . "
		")->getOne();
		return UIAnchor::factory("Options (" . $count . ")")->onClick('options(AF_REFID, event)')->toHTML();
	}

	function optionsshint($data) {
		$names = db::execSQL("
			SELECT ini_name
			  FROM webset.sped_ini_set set
               INNER JOIN webset.sped_ini ini ON set.irefid = ini.irefid
			 WHERE srefid = " . $data['srefid'] . "
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['ini_name'] . "<br/>";
		}
		return $res;
	}

?>
<script>
	function iepblocks(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('IEP Blocks', api.url("./iep_blocksMain.php?iep=" + id));
		win.resize(1200, 700);
		win.show();
	}

	function doctypes(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Doc Types', api.url("./iep_docs_list.php?iepformat=" + id));
		win.resize(1200, 700);
		win.show();
	}
	function options(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Options', api.url("./iniset_list.php?iepformat=" + id));
		win.resize(1200, 700);
		win.show();
	}
</script>
