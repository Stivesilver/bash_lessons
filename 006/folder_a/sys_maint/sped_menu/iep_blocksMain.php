<?PHP
	Security::init();

	$list = new listClass();

	$list->title = "IEP Blocks";

	$list->showSearchFields = "yes";

    $list->SQL = "SELECT ieprefid,
	                     doctype,
	                     iepdesc,
	                     substring(iepurl,1,50) AS substr,
	                     ieprenderfunc,
	                     iepnum,
	                     iepseqnum,
	                     iepinclude,
	                     check_method,
	                     check_param
	                FROM webset.sped_iepblocks
	                     LEFT OUTER JOIN webset.sped_doctype ON drefid = ieptype
	               WHERE iepformat = ". io::get("iep") ."
                         ADD_SEARCH
	               ORDER BY doctype, drefid, seqnum, iepseqnum";


	$list->addSearchField("ID", "(ieprefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
    $list->addSearchField(FFSelect::factory("Doc Type"))
	    ->sqlField('ieptype')
	    ->sql("SELECT drefid,  doctype
                 FROM webset.sped_doctype
                WHERE setrefid = ". io::get("iep") ."
                ORDER BY seqnum, 2"
	    );
    $list->addSearchField('Block', "LOWER(iepdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Path', "LOWER(iepurl)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Render Function', "LOWER(ieprenderfunc)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField("Block ID")->sqlField('iepnum');
    $list->addSearchField("Check Method")->sqlField('check_method');
    $list->addSearchField("Check Parameter")->sqlField('check_param');

	$list->addColumn('ID')->sqlField('ieprefid');
	$list->addColumn("Doc Type", "", "group")->sqlField('doctype');
	$list->addColumn("Block")->sqlField('iepdesc');
	$list->addColumn("Path")->sqlField('substr');
	$list->addColumn("Render Function")->sqlField('ieprenderfunc');
	$list->addColumn("Block ID")->sqlField('iepnum');
	$list->addColumn("Included File")->sqlField('iepinclude');
	$list->addColumn("Check Method")->sqlField('check_method');
	$list->addColumn("Check Parameter")->sqlField('check_param');
	$list->addColumn("Order #")->sqlField('iepseqnum');

	$list->addURL   = "iep_blocksAdd.php?iep=". io::get("iep");
	$list->editURL  = "iep_blocksAdd.php?iep=". io::get("iep");
	$list->deleteTableName = "webset.sped_iepblocks";
	$list->deleteKeyField  = "ieprefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sped_iepblocks')
			->setKeyField('ieprefid')
			->applyListClassMode()
	);

	$list->printList();


?>
