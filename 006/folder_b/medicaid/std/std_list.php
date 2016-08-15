<?php

	Security::init();
	FIFParts::init();

	$list = new ListClass();
	$list->title            = 'Student Medicaid Management';
	$list->showSearchFields = true;
	$list->printable        = true;
	$list->editURL          = 'javascript:openEdit(AF_REFID)';

	$list->SQL = "
        SELECT std.stdrefid,
		       stdlnm  || ', ' || stdfnm || RTRIM(' ' || LTRIM(COALESCE(SUBSTRING(stdmnm FROM '[[:alnum:]]'), '') || '.', '.'), ' ') AS student,
		       std.stdrefid as stdid,
		       std.externalid,
		       gl_code,
		       vouname,
		       std.vndrefid,
               CASE WHEN COALESCE(stdstatus,'A') = 'A' THEN 'Y' ELSE 'N' END as stdstatus,
		       REPLACE(std.stdlnm, '''', '`') AS stdlnm,
		       REPLACE(std.stdfnm, '''', '`') AS stdfnm,
               std.gl_refid,
               std.vourefid,
               stdstateidnmbr,
               stdmedicatenum,
               med.msm_medicaid,
               msm_medicaid_eligible_sw
	      FROM webset.vw_dmg_studentmst AS std
	           LEFT OUTER JOIN c_manager.def_grade_levels gl  ON std.gl_refid = gl.gl_refid
	           LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.vourefid
	           LEFT OUTER JOIN webset.med_std_main med ON med.stdrefid = std.stdrefid
         WHERE std.vndrefid = VNDREFID
               ADD_SEARCH
	     ORDER BY UPPER(stdlnm), UPPER(stdfnm)
	";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField("Lumen ID #", "std.stdrefid");
	$list->addSearchField("Medicaid #", "msm_medicaid");
	$list->addSearchField("External ID #", "std.externalid");

	$list->addSearchField(FFSwitchYN::factory('Medicaid Eligible'))
		->sqlField('msm_medicaid_eligible_sw')
		->value(-1);

	$list->addSearchField(FFIDEASchool::factory(true), '')
		->name('vourefid');

	$list->addSearchField(FFSelect::factory('Entroment Status'), '')
		->data(array(
			1 => 'Student Currently Enrolled',
			2 => 'Enrolled During Selected School Year'
		))
		->name('status');

	$list->addColumn('Student')
		->sqlField('student');

	$list->addColumn('School')
		->sqlField('vouname')
		->type('repeat');

	$list->addColumn("Medicaid #")
		->sqlField('msm_medicaid');

	$list->addColumn("Lumen Student ID #")
		->sqlField('stdid');

	$list->addColumn("External ID #")
		->sqlField('externalid');

	$list->addColumn("State ID #")
		->sqlField('stdstateidnmbr');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.dmg_studentmst')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();

?>

<script type="text/javascript">

	function openEdit(id) {
		var url = api.url(
			'std_edit.php',
			{'stdrefid': id}
		);
		var win = api.desktop.open('Loading...', url);
		win.maximize();
		win.addEventListener(WindowEvent.CLOSE, formCompleted);
	}

	function formCompleted() {
		var list = ListClass.get();
		list.reload();
	}

</script>
