<?PHP
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT actrefid,
               catname,
               actname,
               acc.seqnum,
               CASE WHEN NOW() > cat.enddate THEN 'N' ELSE 'Y' END AS catstatus,
               CASE WHEN NOW() > acc.enddate THEN 'N' ELSE 'Y' END AS status
          FROM webset.statedef_aa_act_acc AS acc
               INNER JOIN webset.statedef_aa_act_cat AS cat ON cactrefid = actcat
         WHERE 1=1 ADD_SEARCH
         ORDER BY cat.seqnum, cat.catname, acc.seqnum, acc.actname
    ";

	$list->title = "Form D - ACT Accommodations";

	$list->addSearchField("ID", "(actrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Accommodation", "", "TEXT")->sqlField("lower(accname)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addSearchField(FFSwitchYN::factory('Category Status'))->sqlField("(CASE cat.enddate<now() WHEN true THEN 'N' ELSE 'Y' END)")->value('Y');
	$list->addSearchField(FFSwitchYN::factory('Accommodation Status'))->sqlField("(CASE acc.enddate<now() WHEN true THEN 'N' ELSE 'Y' END)")->value('Y');

	$list->addColumn('ID')->sqlField('actrefid');
	$list->addColumn("Category", "", "group")->sqlField('catname');
	$list->addColumn("Accommodation", "", "text")->sqlField('actname');
	$list->addColumn("Seq", "", "text")->sqlField('seqnum');
	$list->addColumn("Cat Status", "", "text")->sqlField('catstatus')->type('switch');
	$list->addColumn("Acc Status", "", "text")->sqlField('status')->type('switch');

	$list->editURL = CoreUtils::getURL('./act_acc_add.php');
	$list->addURL = CoreUtils::getURL('./act_acc_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_aa_act_acc')
			->setKeyField('actrefid')
			->applyListClassMode()
	);

	$list->deleteTableName = "webset.statedef_aa_act_acc";
	$list->deleteKeyField = "actrefid";

	$list->printList();

?>
