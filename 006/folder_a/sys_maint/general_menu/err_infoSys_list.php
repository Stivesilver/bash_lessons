<?PHP
	Security::init();

	$list = new listClass();

	$list->multipleEdit = false;

	$list->title = "Error Information Systems";

	$list->SQL = "
		SELECT isrefid, isdesc, lastuser, to_char(lastupdate, 'MM-DD-YY HH:MI')
          FROM webset.err_infosysdef WHERE (1=1) ADD_SEARCH
         ORDER BY isdesc
	";

	$list->addSearchField("ID", "(isrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Info System", "isdesc", "text", "", "", "");
	$list->addColumn('ID')->sqlField('isrefid');
	$list->addColumn("Info System", "70%", "TEXT", "", "");
	$list->addColumn("Last User", "12%", "TEXT", "", "");
	$list->addColumn("Last Update", "18%", "TEXT", "", "");

	$list->addURL = "err_infoSysAdd.php";
	$list->editURL = "err_infoSysAdd.php";
	$list->deleteTableName = "webset.err_infosysdef";
	$list->deleteKeyField = "isrefid";

	$defs = new exportDefClass();
	$defs->curlist = $list;

	$list->addButton($defs->addExportButton());

	$list->printList();
?>
