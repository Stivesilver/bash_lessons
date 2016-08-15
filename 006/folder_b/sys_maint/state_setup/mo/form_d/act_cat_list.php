<?
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT cactrefid,
               catname,
               catdesc,
               seqnum,
               CASE WHEN NOW() > enddate   THEN 'N' ELSE 'Y' END  AS status
          FROM webset.statedef_aa_act_cat
         WHERE 1=1 ADD_SEARCH
         ORDER BY seqnum, catname
    ";

	$list->title = "Form D - ACT Categories";

	$list->addSearchField("ID", "(cactrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSwitchYN::factory('Category Status'))->sqlField("(CASE enddate<now() WHEN true THEN 'N' ELSE 'Y' END)")->value('Y');

	$list->addColumn('ID')->sqlField('cactrefid');
	$list->addColumn("Category", "", "text")->sqlField('catname');
	$list->addColumn("Category Description", "", "text")->sqlField('catdesc');
	$list->addColumn("Sequence Number", "", "text")->sqlField('seqnum');
	$list->addColumn("Status", "", "text")->sqlField('status')->type('switch');

	$list->editURL = CoreUtils::getURL('./act_cat_add.php');
	$list->addURL = CoreUtils::getURL('./act_cat_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_aa_act_cat')
			->setKeyField('cactrefid')
			->setNesting('webset.statedef_aa_act_acc', 'actrefid', 'actcat', 'webset.statedef_aa_act_cat', 'cactrefid')
			->applyListClassMode()
	);

	$list->deleteTableName = "webset.statedef_aa_act_cat";
	$list->deleteKeyField = "cactrefid";


	$list->printList();
?>
