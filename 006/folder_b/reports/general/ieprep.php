<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Archived IEP';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT siepmrefid,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       COALESCE(siepmtdesc,rptype,'IEP') AS doc,
		       iep.lastupdate,
		       iep.lastuser,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.sys_teacherstudentassignment AS ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
		       " . IDEAParts::get('casemanJoin') . "
		       INNER JOIN webset.std_iep AS iep ON iep.stdrefid = ts.tsrefid
		       LEFT OUTER JOIN webset.statedef_ieptypes types ON iep.siepmtrefid = types.siepmtrefid
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		   AND COALESCE(iep_status, 'A') !='I'
		 ORDER BY 2, iep.lastupdate DESC
		 ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField('Type of Document', "COALESCE(siepmtdesc,rptype,'IEP')  ILIKE '%' || LOWER(ADD_VALUE)|| '%'");
	$list->addSearchField('User Archived', "iep.lastuser  ILIKE '%' || LOWER(ADD_VALUE)|| '%'");
	$list->addSearchField('Date Archived',  'iep.lastupdate::date', 'date_range');
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('um.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Type of Document')->sqlField('doc');
	$list->addColumn('User Archived')->sqlField('lastuser');
	$list->addColumn('Date Archived')->sqlField('lastupdate')->type('date');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->editURL = "javascript:api.ajax.process(ProcessType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/iep_view.ajax.php') . "', {'RefID' : AF_REFID}))";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_iep')
			->setKeyField('siepmrefid')
			->applyListClassMode()
	);

	$list->addButton(
		FFButton::factory('Download')
			->onClick('getReports();')
			->width(80)
	);

	$list->printList();
?>

<script>
	function getReports() {
		var selVal = ListClass.get().getSelectedValues().values.join(',');
		if (selVal != '') {
			api.ajax.process(
				UIProcessBoxType.REPORT,
				api.url('./iep_collect.ajax.php'),
				{
					'selVal' : selVal
				}
			);
		} else {
			alert('Please select Form(s)')
		}
	}
</script>
