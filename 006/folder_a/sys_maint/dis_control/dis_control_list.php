<?php
	Security::init();

	$list = new ListClass();

	$list->showSearchFields = "yes";

	$list->SQL = "
		SELECT dcrefid,
		       dcdesc,
		       dckey,
		       sdc.name,
		       plpgsql_recs_to_str(
		      	 'SELECT state AS column
		            FROM webset.statedef_discontrol
		                 INNER JOIN webset.glb_statemst ON webset.glb_statemst.staterefid = webset.statedef_discontrol.screfid
		           WHERE dcrefid = '||dcrefid, ', '
               ) AS states
		  FROM webset.def_discontrol AS dds
		       LEFT JOIN webset.statedef_discontrol_cat AS sdc ON (dds.sdcatrefid = sdc.sdcatrefid)
			WHERE 1=1 ADD_SEARCH
		 ORDER BY sdc.order_num, sdc.name, dcdesc
    ";

	$list->title = "District Control";

	$list->addSearchField("ID", "(dcrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField("Parameter")->sqlField("lower(dcdesc)  like '%' || lower(ADD_VALUE) || '%'");
	$list->addSearchField(FFSelect::factory())
		->caption('Category')
		->sqlField('dds.sdcatrefid')
		->sql('
			SELECT sdcatrefid,
				   name
			  FROM webset.statedef_discontrol_cat
			 ORDER BY order_num, name
		');

	$list->addColumn('ID')->sqlField('dcrefid');
	$list->addColumn("Category", "", "group")->sqlField('name');
	$list->addColumn("ID", "", "text")->sqlField('dcrefid');
	$list->addColumn("District Control Option")->sqlField('dcdesc');
	$list->addColumn("Key for Option")->sqlField('dckey');
	$list->addColumn("State")->dataCallback('openStates')->sqlField('states');

	$list->addURL = "dis_control_add.php";
	$list->editURL = "dis_control_add.php";

	$list->deleteTableName = "webset.def_discontrol";
	$list->deleteKeyField = "dcrefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_discontrol')
			->setKeyField('dcrefid')
			->setNesting('webset.statedef_discontrol', 'sdcrefid', 'dcrefid', 'webset.def_discontrol', 'dcrefid')
			->applyListClassMode()
	);

	$list->addButton('Category')
		->width('78px')
		->onClick('category()');

	$list->printList();

	function openStates($data) {
		if ($data['states'] != '') {
			$name = $data['states'];
		} else {
			$name = 'All';
		}
		return $name;
	}

?>
<script>
	function category(){
		var win = api.window.open('Category List', api.url("./discat_list.php"));
		win.show();
	}
</script>
world was on fire nobody can save me but you, i never that i'v been somebody like you, now  i i don't wanna fallen love with you with you
