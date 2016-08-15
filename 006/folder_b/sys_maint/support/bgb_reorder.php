<?php
	Security::init();

	$list = new listClass();
	$list->title = 'BGB Order';
	$list->showSearchFields = true;

	$list->SQL = "
        SELECT tsrefid,
               std.stdrefid,
               stdlnm,
               stdfnm,
               " . IDEAParts::get('spedPeriod') . " as spedperiod,
               gl_code,
               vndname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
                       " . IDEAParts::get('studentJoin') . "
                       " . IDEAParts::get('gradeJoin') . "
                       " . IDEAParts::get('enrollJoin') . "
               INNER JOIN sys_vndmst ON sys_vndmst.vndrefid = std.vndrefid
         WHERE ADD_SEARCH
         ORDER BY UPPER(stdlnm), UPPER(stdfnm)
    ";

	$list->addSearchField('Last Name', "LOWER(stdlnm)  like '%' || LOWER('ADD_VALUE')|| '%'");
	$list->addSearchField('First Name', "LOWER(stdfnm)  like '%' || LOWER('ADD_VALUE')|| '%'");
	$list->addSearchField('Lumen #', "std.stdrefid");
	$list->addSearchField('Student #', "LOWER(stdschid)  like '%' || LOWER('ADD_VALUE')|| '%'");
	$list->addSearchField('Federal #', "LOWER(stdfedidnmbr)  like '%' || LOWER('ADD_VALUE')|| '%'");
	$list->addSearchField(FFMultiSelect::factory('District'))
		->setSearchList(
			ListClassContent::factory('District')
				->addColumn('Name')
				->addSearchField('Name', 'vndname')
				->setSQL("
					SELECT vndrefid,
					       vndname
					  FROM sys_vndmst
					 ORDER BY 2
				")
		)->selectAll(true)
		->sqlField('std.vndrefid');
	$list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
	$list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
		->value('A')
		->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
		->name('spedstatus');
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addColumn('Lumen ID');
	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('Sp Ed Period');
	$list->addColumn('Grade');
	$list->addColumn('District');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus');
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus');

	$list->addButton(
		FFButton::factory('Reorder'))
		->width('80px')
		->onClick('reoreder()');

	$list->editURL = 'javascript:reorder(AF_REFID, "AF_COL2", "AF_COL3")';

	$list->printList();

?>
<script type='text/javascript'>
	function reoreder() {
		var refids = ListClass.get().getSelectedValues().values;
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./bgb_reorder.ajax.php'),
			{
				refids: refids.join(',')
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.reload();
			}
		)
	}
</script>
