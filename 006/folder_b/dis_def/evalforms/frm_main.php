<?php
	Security::init();

	$list = new ListClass();

	$list->title = 'Documentation Defaults - PDF';
	$list->showSearchFields = true;

	$list->SQL = "
		SELECT refid,
		       mfcpdesc,
		       mfcdoctitle,
		       dd.lastuser,
			   dd.lastupdate,
			   dd.form_id
		  FROM webset.disdef_defaults AS dd
		       INNER JOIN webset.statedef_forms AS sf ON dd.form_id = sf.mfcrefid
		       INNER JOIN webset.def_formpurpose AS fp ON sf.mfcprefid = fp.mfcprefid
		 WHERE vndrefid = VNDREFID
		   AND area = 'PDF'
		 ORDER BY 2, 3
	";

	$list->addSearchField('Form Title', 'mfcdoctitle')->sqlMatchType(FormFieldMatch::SUBSTRING);
    $list->addSearchField(FFIDEAStatus::factory())->sqlField("CASE WHEN NOW() > sf.recdeactivationdt THEN 'N' ELSE 'Y' END");

	$list->addColumn('Purpose');
	$list->addColumn('Title');
	$list->addColumn('Completed By');
	$list->addColumn('Completed On')->type('datetime');

	$list->multipleEdit = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.disdef_defaults')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$key = SystemCore::$Registry->readKey('webset', 'districts_groups', 'xml');

	$list->addButton(
		IDEAGroupPopulate::factory()
			->applyListClassMode()
			->setTable('webset.disdef_defaults')
			->setKeyField('refid')
			->setNameField('mfcdoctitle')
			->setContField('values')
			->addJoin('LEFT JOIN webset.statedef_forms AS t2 ON (t.form_id = t2.mfcrefid)')
			->addKeys('form_id')
	);

	$list->deleteTableName = "webset.disdef_defaults";
	$list->deleteKeyField = "refid";

	$list->editURL = "javascript:editForm('AF_REFID', 'AF_COL5');";

	$list
		->addButton('Add New')
		->width('80px')
		->balloon(
			UIBalloon::factory()
				->showInTopFrame(false)
				->addHTML('Add New Document')
				->addObject(
					UILayout::factory()
						->newLine()
						->addObject(
							FFSelect::factory('')
								->name('state_id')
								->sql("
				SELECT f.mfcrefid,
					   p.mfcpdesc || ' / ' || f.mfcdoctitle
				  FROM webset.statedef_forms AS f
                       INNER JOIN webset.def_formpurpose AS p ON  p.mfcprefid  = f.mfcprefid
				 WHERE f.screfid=" . VNDState::factory()->id . "
				   AND f.mfcfilename IS NOT NULL
				   AND (f.recdeactivationdt IS NULL OR now()< f.recdeactivationdt)
				   AND COALESCE(f.onlythisip,'" . SystemCore::$VndName . "') LIKE '%" . SystemCore::$VndName . "%'
				 ORDER BY p.mfcpdesc, f.mfcdoctitle
								")
								->emptyOption(true)
								->onChange('editForm(0, $("#state_id").val())')
						)
				)
		);

	$list->printList();

?>
<script type="text/javascript">
	function editForm(refid, state_id) {
		if (state_id > 0) {
			var win = api.window.open(
				'Edit Form',
				api.url(
					'./frm_add.ajax.php'),
				{
					'refid': refid,
					'state_id': state_id
				}
			);
			win.addEventListener('form_saved', onEvent);
			win.maximize();
			win.show();
		}
	}

	function onEvent(e) {
		api.reload();
	}

</script>
