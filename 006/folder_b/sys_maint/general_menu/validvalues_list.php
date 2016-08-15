<?PHP
	Security::init();

	$list = new listClass();

	// --- General Properties ---
	$list->title = "Valid Values";

	$list->showSearchFields = true;


	// --- SQL Query for the List ---
	$list->SQL = "
		SELECT refid/*PK - added by System*/,
               valuename AS valuename,
               validvalue AS validvalue,
               validvalueid AS validvalueid,
               sequence_number AS sequence_number
               FROM (SELECT refid,
                            valuename,
                            validvalue,
                            validvalueid,
                            sequence_number,
                            lastuser,
                            lastupdate
                       FROM webset.glb_validvalues
                      WHERE 1 = 1 ADD_SEARCH)
             AS main_list_table
       ORDER BY valuename, sequence_number, validvalue ASC";

	// --- List Search Fields ---
	$list->addSearchField("ID", "(refid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Category", "valuename", "TEXT");
	$list->addSearchField("Value Text", "validvalue", "TEXT");


	// --- List Columns ---
	$list->addColumn('ID')->sqlField('refid');
	$list->addColumn("Category", "", "TEXT")->sqlField('valuename');
	$list->addColumn("Value Text", "", "TEXT")->sqlField('validvalue');
	$list->addColumn("Value ID", "", "TEXT")->sqlField('validvalueid');
	$list->addColumn("Sequence Number", "", "TEXT")->sqlField('sequence_number');

	$list->addURL = CoreUtils::getURL('./validvalues_add.php');
	$list->editURL = CoreUtils::getURL('./validvalues_add.php');

	$list->deleteTableName = "webset.glb_validvalues";
	$list->deleteKeyField = "refid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_spedstatus')
			->setKeyField('stsrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
