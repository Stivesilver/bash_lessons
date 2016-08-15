<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid = $ds->safeGet('stdrefid');

	$list = new ListClass('list1');

	$list->title = 'FB Documentation';

	$list->deleteTableName = 'webset.std_forms';
	$list->deleteKeyField = 'smfcrefid';

	$list->SQL = "
		SELECT smfcrefid,
			   CASE WHEN df.dfrefid IS NOT NULL THEN title ELSE mfcdoctitle END AS title,
			   TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || COALESCE(' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY'),'') AS siymdate,
			   forms.lastuser,
			   forms.lastupdate
		  FROM webset.std_forms AS forms
		 	   INNER JOIN webset.sys_teacherstudentassignment AS ts ON forms.stdrefid = ts.tsrefid
		   	   LEFT OUTER JOIN webset.std_iep_year AS years ON years.siymrefid = forms.iepyear
		   	   LEFT JOIN webset.disdef_forms AS df ON df.dfrefid = forms.dfrefid
		   	   LEFT JOIN webset.statedef_forms AS sf ON (sf.mfcrefid = forms.mfcrefid AND sf.fb_content IS NOT NULL)
		 WHERE ts.stdrefid = " . $stdrefid . "
		   AND forms.fb_content IS NOT NULL
			   " . (IDEACore::disParam(50) == 'Y' ? "AND iepyear = " . $stdIEPYear : "") . "
		ORDER BY lastupdate DESC
	";

	$list->addColumn('Title')->sqlField('title');
	$list->addColumn('IEP Year')->sqlField('siymdate');

	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('datetime');

	$list->editURL = 'javascript: openSubmission(AF_REFID);';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFButton::factory('Add New')
			->leftIcon('plus.png')
			->onClick('addForm();')
	);

//	$list->addButton(
//		FFButton::factory('Add New')
//			->leftIcon('plus.png')
//			->balloon(
//				UIBalloon::factory()
//					->showInTopFrame(false)
//					->showCloseButton(true)
//					->addObject(
//						UILayout::factory()
//							->addHTML(
//								FFSelect::factory()
//									->name('templ_id')
//									->caption('Template')
//									->sql("
//										SELECT 1 || '_' || dfrefid,
//										       title
//										  FROM webset.disdef_forms AS df
//										 WHERE df.vndrefid = " . SystemCore::$VndRefID . "
//										 UNION
//										SELECT 2 || '_' ||stf.mfcrefid,
//										       mfcdoctitle
//										  FROM webset.statedef_forms AS stf
//										 WHERE stf.screfid = " . VNDState::factory()->id . "
//										   AND fb_type = 1
//										 ORDER BY 2
//									")
//									->toHTML(),
//								'[padding: 10px 15px]'
//							)
//							->addDividingLine()
//							->addObject(
//								FFButton::factory('Create & Open')
//									->onClick('openSubmission(0, $("#templ_id").val()); UIBalloon(this).destroy()'),
//								'[padding: 4px] center'
//							)
//					)
//			)
//	);

	$list->printList();

	io::jsVar('dskey', $dskey);

?>
<script>

	function openSubmission(f_refid, t_refid) {
		if (!t_refid) {
			t_refid = 0;
		}
		var wnd = api.window.open(
			'Form Submission',
			api.url('./fb_form_view.php', {'f_refid': f_refid, 't_refid': t_refid, 'dskey': dskey})
		);
		wnd.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				ListClass.get().reload();
			},
			this
		);
		wnd.maximize();
		wnd.show();
	}

	function addForm() {
		var wnd = api.window.open(
			'Form Submission',
			api.url('./fb_frm_add.php', {'dskey': dskey})
		);
		wnd.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				ListClass.get().reload();
			},
			this
		);
		wnd.show();
	}

</script>
