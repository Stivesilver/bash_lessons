<?php
	Security::init();
	FIFParts::init();

    $list = new ListClass();

    $list->showSearchFields = true;
    $list->printable = true;

	$sqlList = "
		SELECT *
          FROM (SELECT std.stdrefid,
				       stdlnm  || ', ' || stdfnm || RTRIM(' ' || LTRIM(COALESCE(SUBSTRING(stdmnm FROM '[[:alnum:]]'), '') || '.', '.'), ' '),
				       std.stdrefid AS stdid,
				       std.externalid,
				       gl_code,
				       vouname,
	                   CASE WHEN COALESCE(stdstatus,'A') = 'A'
							THEN 'Y'
							ELSE 'N'
					   END as stdstatus,
	                   CASE WHEN " . FIFParts::get('fifActive') . "
							THEN 'Y'
							ELSE 'N'
					   END as fifstatus,
				       std.stdlnm,
				       std.stdfnm,
	                   std.gl_refid,
	                   std.vourefid,
	                   ts.umrefid
			      FROM webset.vw_dmg_studentmst AS std
			           INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
			           LEFT OUTER JOIN c_manager.def_grade_levels gl ON std.gl_refid = gl.gl_refid
			           LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.vourefid
		         WHERE std.vndrefid = VNDREFID
			   ) AS std
	     WHERE 1=1 ADD_SEARCH
		 ORDER BY UPPER(stdlnm), UPPER(stdfnm)
	";

	$a = FFIDEACaseManager::factory('pc')
		->name('umrefid')
		->sqlField('umrefid')
		->emptyOption(false);

	$defCM = db::execSQL($a->sql)->getOne();

	if ($defCM == '') {
		$defCM = '-1';
		$sqlList = str_replace("ADD_SEARCH", "AND 1=0 ADD_SEARCH", $sqlList);
		$a->hide();
	}

	$SQL = "
						SELECT t3.umlastname || ', ' || t3.umfirstname
						  FROM webset.sys_proccoordmst AS t1
							   INNER JOIN webset.sys_proccoordassignment AS t2 ON t2.pcrefid = t1.pcrefid
							   INNER JOIN public.sys_usermst AS t3 ON t3.umrefid = t1.umrefid
							   INNER JOIN webset.sys_casemanagermst cm ON cm.umrefid = t3.umrefid
					";
	if (io::posti('umrefid') > 0) {
		$SQL .= ' WHERE t2.cmrefid = ' . io::post('umrefid');
	} else {
		$SQL .= ' WHERE t2.cmrefid = ' . $defCM;
	}

	$PCName = db::execSQL($SQL)->getOne();
	$a->value($defCM);
	if ($PCName != '') $a->append(UILayout::factory()->addHTML('', '2%')->addHTML('<b>PC:</b> ' . $PCName));

	$list->SQL = $sqlList;

	$list->addSearchField($a);

    $list->addSearchField(FFStudentName::factory()->searchMethod(FormFieldMatch::START_FROM));
	$list->addSearchField("Lumen ID #", "std.stdrefid");
	$list->addSearchField("External ID #", "std.externalid");
	$list->addSearchField(FFSwitchYN::factory("Active Student"), "std.stdstatus")->value('Y');
    $list->addSearchField(FFSwitchYN::factory('504 Active'), "std.fifstatus");
	$list->addSearchField(FFGradeLevel::factory('std.gl_refid'));
	$list->addSearchField("Reporting School", "vourefid", "list")
		->name('vourefid')
		->sql("
			SELECT vourefid,
	               vouname
	          FROM sys_voumst
	         WHERE vndrefid = VNDREFID
	         ORDER by vouname
		");

	$list->addColumn("Student")->type("repeat");
	$list->addColumn("Lumen ID #");
	$list->addColumn("External ID #");
	$list->addColumn('Grade Level');
	$list->addColumn("School");
    $list->addColumn('Active Student')->hint('Student Status')->type('switch')->sqlField('stdstatus');
    $list->addColumn('504 Active')->hint('504 Status')->type('switch')->sqlField('fifstatus');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.vw_dmg_studentmst')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->prepareRow('prepareRow');

    $list->printList();

	function prepareRow(ListClassRow $row) {
		$row->onClick("openStdDetails(" .
			json_encode($row->data['stdrefid']) . ", " .
			json_encode($row->data['stdfnm']) . ", " .
			json_encode($row->data['stdlnm']) . ");");
	}
?>
<script>
    function openStdDetails(refid, fname, lname) {
        var wnd = api.desktop.open(fname + ' ' + lname + ', Lumen ID: ' + refid + ' - 504 Manager', api.url('std_history.php', {stdrefid: refid}));
        wnd.maximize();
    }
</script>
