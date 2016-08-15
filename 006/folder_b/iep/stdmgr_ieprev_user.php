<?PHP
	Security::init();

	$list = new listClass();

	$list->showSearchFields = "yes";

	$list->title = "IEP Format";

	$list->SQL = "
		SELECT tsrefid,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       " . IDEAParts::get('schoolName') . " AS vouname,
		       gl.gl_refid,
		       " . IDEAParts::get('spedPeriod') . " AS spedperiod,
		       NULL AS iep,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.sys_teacherstudentassignment ts " . IDEAParts::get('studentJoin') . " " . IDEAParts::get('enrollJoin') . " " . IDEAParts::get('schoolJoin') . " " . IDEAParts::get('gradeJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY UPPER(stdLNM), UPPER(stdFNM), UPPER(stdMNM)
    ";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	if (io::get('access') == 'admin') {
		$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	} else {

		$SQL = "
			SELECT t3.umlastname||',
			       '||t3.umfirstname AS pcname
			  FROM webset.sys_proccoordmst AS t1
			       INNER JOIN webset.sys_proccoordassignment AS t2 ON t2.pcrefid = t1.pcrefid
			       INNER JOIN public.sys_usermst AS t3 ON t3.umrefid = t1.umrefid
			       INNER JOIN webset.sys_casemanagermst cm ON cm.umrefid = t3.umrefid
		";
		if (io::post("umrefid")) {
			$SQL .= " WHERE t2.cmrefid = " . io::post("umrefid");
		}
		$result = db::execSQL($SQL);
		$PCName = str_replace("'", "\'", $result->fields["pcname"]);

		$a = FFIDEACaseManager::factory('pc')
			->name('umrefid')
			->sqlField('ts.umrefid')
			->emptyOption(false);
		if ($PCName) {
			$a->append(UIMessage::factory('<b>PC</b>: ' . $PCName)->type(UIMessage::NOTE));
		}

		$list->addSearchField($a);
	}
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());


	$list->title = "Student IEP Review";

	$list->addColumn("Student")->sqlField('stdname');
	$list->addColumn("Attending School")->sqlField('vouname');
	$list->addColumn("Grade")->sqlField('gl_refid');
	$list->addColumn("Sp Ed Enrollment")->sqlField('spedperiod');
	$list->addColumn("IEP")->sqlField('iep')->dataCallback('reviewIEP');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();

	function reviewIEP($data) {
		$names = db::execSQL("
			SELECT siepmrefid,
			       COALESCE(siepmtdesc,rptype,siepmerrlogfilenm,'''') || ': ' || to_char(COALESCE(s.lastupdate,s.siepmdocdate), 'MM-DD-YYYY') AS iepname
			  FROM webset.std_iep s
			       LEFT OUTER JOIN webset.statedef_ieptypes t ON s.sIEPMTRefID = t.sIEPMTRefId
			 WHERE stdrefid = (" . $data['tsrefid'] . ")
			   AND COALESCE(iep_status, 'A') != 'I'
			 ORDER BY s.lastupdate, s.siepmdocdate DESC
		")->assocAll();
		if ($names) {
			$layout = UILayout::factory();
			foreach ($names as $name ) {
				$layout->addObject(UIAnchor::factory($name['iepname'])->onClick("reviewIEP(" . $name['siepmrefid'] . ", event)"))->newLine();
			}
			return $layout->toHTML();
		} else {
			return UIAnchor::factory("")->toHTML();
		}
	}

?>
<script>
	function reviewIEP(id, evt) {
		api.event.cancel(evt);
		api.ajax.process(UIProcessBoxType.PROCESSING, api.url('../library/iep_view.ajax.php'), {'RefID': id});
	}
</script>
