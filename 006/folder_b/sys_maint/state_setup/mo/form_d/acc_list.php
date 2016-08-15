<?PHP
	Security::init();

	$staterefid = io::get('staterefid');

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT accrefid,
               catdesc,
               acccode,
               accdesc,
               plpgsql_recs_to_str('SELECT cast(progdesc as varchar) AS column
                                      FROM webset.statedef_aa_prog subj
                                     WHERE subj.progrefid in (' || COALESCE(cat,'0') || ') 
                                     ORDER BY seqnum', ', ') as cat,
               seq_num,
               CASE WHEN NOW() > aa_acc.enddate THEN 'I' ELSE 'A' END as status
          FROM webset.statedef_aa_acc aa_acc
		       INNER JOIN webset.statedef_aa_cat aa_cat ON aa_acc.acccat = aa_cat.catrefid
         WHERE aa_cat.screfid = " . $staterefid . " ADD_SEARCH
         ORDER BY seq_num, accdesc
    ";

	$list->title = "Form D - Assessment";

	$list->addSearchField("ID", "(accrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory("Subject"))
		->sql("
			SELECT progdesc,
                   progdesc
              FROM webset.statedef_aa_prog
             WHERE (enddate IS NULL or now()< enddate)
               AND part2 = 'Y'
             ORDER BY seqnum, progdesc
		")
		->sqlField('catdesc');

	$list->addSearchField(FFSelect::factory("Category"))
		->sql("
			SELECT catrefid,
                   catdesc
              FROM webset.statedef_aa_cat
             ORDER BY catdesc
		")
		->sqlField('aa_cat.catrefid');

	$list->addSearchField("Assessment Code")->sqlField('acccode');
	$list->addSearchField("Assessment Description")->sqlField('accdesc');
	$list->addSearchField(FFSwitchAI::factory('Status'))
		->sqlField("CASE WHEN NOW() > aa_acc.enddate THEN 'I' ELSE 'A' END")
		->value('A'); 

	$list->addColumn('ID')->sqlField('accrefid');
	$list->addColumn("Subject/Category", "", "group")->sqlField('catdesc');
	$list->addColumn("Assessment Code", "", "text")->sqlField('acccode');
	$list->addColumn("Assessment Description", "", "text")->sqlField('accdesc');
	$list->addColumn("Cat", "", "text")->sqlField('cat');
	$list->addColumn("Sequence", "", "text")->sqlField('seq_num');
	$list->addColumn("Status")->sqlField('status')->type('switch');


	$list->editURL = CoreUtils::getURL('./acc_add.php', array('staterefid' => $staterefid));
	$list->addURL = CoreUtils::getURL('./acc_add.php', array('staterefid' => $staterefid));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_aa_acc')
			->setKeyField('accrefid')
			->applyListClassMode()
	);

	$list->deleteTableName = "webset.statedef_aa_acc";
	$list->deleteKeyField = "accrefid";

	$list->printList();

?>
