<?php
	Security::init();

	$dskey = io::get('dskey');

	$list = new ListClass('list1');

	$list->title = 'Add New Form';

	$list->showSearchFields = true;

	$list->SQL = "
		 SELECT f.efrefid,
               f.form_title
          FROM webset.es_disdef_evalforms AS f
         WHERE f.vndrefid = VNDREFID
           AND (
				   f.recdeactivationdt IS NULL
				OR now() < f.recdeactivationdt
               )
           AND (
				   f.form_xml IS NOT NULL
				OR EXISTS (
					SELECT 1
					  FROM webset.statedef_forms AS s
					 WHERE s.mfcrefid = f.stateform_id
					   AND s.xmlform_id IS NOT NULL
				   )
               )
         ORDER BY f.form_title
";

	$list->addSearchField('Form Title')->sqlField('form_title');

	$list->addColumn('Title')->sqlField('form_title');

	$list->editURL = 'javascript: editForm("AF_REFID");';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_disdef_evalforms')
			->setKeyField('efrefid')
			->applyListClassMode()
	);

	$list->printList();

	io::jsVar('dskey', $dskey);
?>
<script type="text/javascript">
	function editForm(RefID) {
			var win = api.window.open(
				'Edit Form',
				api.url(
					'./form_add.ajax.php'),
				{
					'dskey': dskey,
					'stateform': RefID,
					'add': 1,
					'std_id': -1
				}
			);
			win.addEventListener('form_saved', onEvent);
			win.maximize();
	}

	function onEvent(e) {
		api.window.dispatchEvent("forms_imported");
		api.window.destroy();
	}
</script>
