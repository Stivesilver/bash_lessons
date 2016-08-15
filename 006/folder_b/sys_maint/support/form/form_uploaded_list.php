<?php
    Security::init();

	$list = new ListClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT * FROM (SELECT smfcrefid,
		       stdlnm || ' ' || stdfnm AS name,
		       smfcdate,
		       webset.std_forms.lastuser AS userlog,
		       mfcpdesc,
		       uploaded_name,
		       tsrefid,
		       CASE WHEN xml_content is not NULL THEN 'xml' ELSE 'pdf' END
		  FROM webset.std_forms
		       LEFT JOIN webset.statedef_forms ON webset.std_forms.MFCRefId = webset.statedef_forms.MFCRefId
		       LEFT JOIN webset.def_formpurpose ON webset.statedef_forms.MFCpRefId = webset.def_formpurpose.MFCpRefId
		       INNER JOIN webset.sys_teacherstudentassignment ON webset.sys_teacherstudentassignment.tsrefid = webset.std_forms.stdrefid
		       INNER JOIN webset.dmg_studentmst ON webset.sys_teacherstudentassignment.stdrefid = webset.dmg_studentmst.stdrefid
		 WHERE uploaded_content IS NOT NULL
			ADD_SEARCH
		 ORDER BY 2, webset.std_forms.smfcdate desc ) AS s ORDER BY 2 ASC
	";

	$list->addSearchField(FFStudentName::factory());
    $list->addSearchField('Uploaded Form Name', "uploaded_name")->sqlMatchType(FormFieldMatch::SUBSTRING);
    $list->addSearchField('Last User', "webset.std_forms.lastuser")->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->title = "PDF Uploaded Forms ";

	$list->addColumn('Student Name')->sqlField('name');
	$list->addColumn('Date')->sqlField('smfcdate');
	$list->addColumn('User Login')->sqlField('userlog');
	$list->addColumn('Form name')->sqlField('uploaded_name');

	$list->hideCheckBoxes = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_forms')
			->setKeyField('smfcrefid')
			->applyListClassMode()
	);

	$list->addButton('Move')
		->width('78px')
		->onClick('movePdf()');

	$list->printList();

?>
<script>
	function movePdf(){
		var selVal = ListClass.get().getSelectedValues().values.join(',');
		if (selVal != '') {
			api.ajax.process(
				UIProcessBoxType.REPORT,
				api.url('./move_pdf.ajax.php'),
				{
					'selVal' : selVal
				}
			).addEventListener(
				ObjectEvent.COMPLETE,
				function (e) {
					api.reload();
				}
			);
		} else {
			alert('Please select Form(s)')
		}
	}
</script>
