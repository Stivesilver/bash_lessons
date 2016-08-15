<?php

	Security::init();

	$list = new listClass();
	$list->customSearch = "yes";
	$list->showSearchFields = "yes";
	$list->multipleEdit = "no";

	$list->SQL="
		SELECT siepmrefid,
               vndname,
               vourefid,
               stdlnm || ' ' || stdfnm AS stdname,
               webset.std_iep.lastuser,
               to_char(webset.std_iep.lastupdate, 'mm-dd-yyyy') AS update,
			   'XML',
			   'View'
          FROM webset.std_iep
               INNER JOIN webset.sys_teacherstudentassignment ON webset.sys_teacherstudentassignment.tsrefid = webset.std_iep.stdrefid
               INNER JOIN webset.dmg_studentmst ON webset.sys_teacherstudentassignment.stdrefid = webset.dmg_studentmst.stdrefid
               INNER JOIN sys_vndmst ON sys_vndmst.vndrefid = webset.dmg_studentmst.vndrefid
         WHERE xml_cont IS NOT NULL AND lower(encode(decode(xml_cont, 'base64'),'escape'))  like '%' || lower(E'/uplinkos/temp/')|| '%'
         ADD_SEARCH
         ORDER BY 2, 4
    ";

	$list->title = "XML IEP Signature";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField("Last name")->sqlField("lower(stdlnm)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addSearchField("First name")->sqlField("lower(stdfnm)  like '%' || lower(ADD_VALUE)|| '%'");
	$list->addSearchField("Text in Body (XML)")->sqlField("lower(encode(decode(xml_cont, 'base64'),'escape'))  like '%' || lower(ADD_VALUE)|| '%'");

	$list->addColumn("District", "", "text")->sqlField('vndname');
	$list->addColumn("Location", "", "text")->sqlField('vourefid');
	$list->addColumn("Student name", "", "text")->sqlField('stdname');
	$list->addColumn("Last User", "", "text")->sqlField('lastuser');
	$list->addColumn("Last Update", "", "text")->sqlField('update');
	$list->addColumn("Edit XML")->dataCallback('editXml');
	$list->addColumn("View IEP")->dataCallback('ViewIEP');

	$list->hideCheckBoxes = false;


	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_iep')
			->setKeyField('siepmrefid')
			->applyListClassMode()
	);

	$list->addButton('Restore')
		->width('78px')
		->onClick('restorePic()');

	$list->printList();

	function ViewIEP($data) {
		return UIAnchor::factory('View')->onClick('ViewIEP(' . $data['siepmrefid'] .')')->toHTML();
	}

	function editXml($data) {
		return UIAnchor::factory('XML')->onClick('editXml(' . $data['siepmrefid'] .')')->toHTML();
	}
?>
<script>
	function ViewIEP(refid) {
		var win = api.window.open('View IEP', "<?=$g_virtualRoot;?>/applications/webset/library/iepView.php?RefID=" + refid);
		win.show();
	}

	function editXml(RefID) {
		var win = api.window.open('Form Edit and Test', '<?=$g_virtualRoot;?>/applications/webset/support/form.php?area=std_iep&form_id='+RefID, true);
		win.moveTo(50,50);
		win.resize(900, 700);
		win.show();
	}

	function restorePic(){
		var selVal = ListClass.get().getSelectedValues().values.join(',');
		if (selVal != '') {
			api.ajax.process(
				UIProcessBoxType.PROGRESS,
				api.url('./restore_pic.ajax.php'),
				{
					'selVal' : selVal
				}
//			).addEventListener(
//				ObjectEvent.COMPLETE,
//				function (e) {
//					api.reload();
//				}
			);
		} else {
			alert('Please select Form(s)')
		}
	}
</script>
