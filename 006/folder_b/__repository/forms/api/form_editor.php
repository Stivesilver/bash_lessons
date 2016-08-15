<?php
	Security::init();

	$fkey = io::get('fkey');
	$ds = DataStorage::factory($fkey);
	$name = $ds->get('name');
	$xml = $ds->get('xml');
	$refid = $ds->get('refid');
	$table = $ds->get('table');
	$key_field = $ds->get('key_field');
	$name_field = $ds->get('name_field');
	$xml_field = $ds->get('xml_field');

	$edit = new EditClass('edit1', $refid);

	$edit->setSourceTable($table, $key_field);

	$edit->title = $name;

	$edit->addTab('Edit')
//		->width(100)
	;

	$edit->addGroup('General Information');

	$edit->topButtons = true;

	$edit->firstCellWidth = 0;

	$edit->addControl('', 'textarea')
		->width('100%')
		->css('height', '509px')
		->value($xml)
		->onChange('formPdf();')
		->allowHTML(true)
		->name('xml');

	$edit->addControl('Last User', 'hidden')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'hidden')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable($table)
			->setKeyField($key_field)
			->applyEditClassMode()
	);

	$edit->addTab('PDF')
	;

	$edit->addIFrame(CoreUtils::getURL('./form_tab.php', array('fkey' => $fkey, 'format' => 'pdf', 'edit' => 2)))
		->height(700)
		->id('pdf_frame');

	$edit->addTab('HTML')
	;

	$edit->addIFrame(CoreUtils::getURL('./form_tab.php', array('fkey' => $fkey, 'format' => 'html', 'edit' => 2)))
		->height(700)
		->id('html_frame');

	$edit->addTab('HTML (fields)');

	$edit->addIFrame(CoreUtils::getURL('./form_tab.php', array('fkey' => $fkey, 'format' => 'html', 'edit' => 1)))
		->height(700)
		->id('html_fields_frame');

	$edit->addTab('ODT')
	;

	$edit->addIFrame(CoreUtils::getURL('./form_odt.php', array('fkey' => $fkey, 'format' => 'odt', 'edit' => 2)))
		->height(700)
		->id('odt_frame');

	$edit->addTab('RC PDF');

	$edit->addIFrame(CoreUtils::getURL('./form_rc_tab.php', array('fkey' => $fkey, 'format' => 'pdf', 'edit' => 2)))
		->height(700)
		->id('rc_pdf_frame');

	$edit->addTab('RC HTML')
	;

	$edit->addIFrame(CoreUtils::getURL('./form_rc_tab.php', array('fkey' => $fkey, 'format' => 'html', 'edit' => 2)))
		->height(700)
		->id('rc_html_frame');

	$edit->addTab('Check')
	;

	$edit->addIFrame(
		CoreUtils::getURL('./form_check.php', array('fkey' => $fkey, 'format' => 'html', 'edit' => 2))
	)->height(700)
		->id('check_frame');

	$edit->getButton(EditClassButton::CANCEL)
		->value('Close');

	$edit->onSaveDone = 'offReload';

	$edit->cancelURL = $ds->get('url_finish');

	$edit->saveAndEdit = true;

	$edit->saveAndAdd = false;

	$edit->setPresaveCallback('form_editor.ajax.php', $save_url);

	$edit->printEdit();

	function offReload() {
		return false;
	}
?>

<script>
	$("#xml").focus().select();

	function formPdf() {
		api.remoteCall(
			api.url('./form_tab.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'pdf',
				'edit': 2
			},
			'pdf_frame'
		);
		api.remoteCall(
			api.url('./form_rc_tab.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'pdf',
				'edit': 2
			},
			'rc_pdf_frame'
		);
		api.remoteCall(
			api.url('./form_tab.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'html',
				'edit': 2
			},
			'html_frame'
		);
		api.remoteCall(
			api.url('./form_tab.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'html',
				'edit': 1
			},
			'html_fields_frame'
		);
		api.remoteCall(
			api.url('./form_rc_tab.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'html',
				'edit': 2
			},
			'rc_html_frame'
		);
		api.remoteCall(
			api.url('./form_odt.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'odt',
				'edit': 2
			},
			'odt_frame'
		);
		api.remoteCall(
			api.url('./form_check.php'),
			null,
			{
				'xml': $('#xml').val(),
				'format': 'odt',
				'edit': 2
			},
			'check_frame'
		);
	}
</script>
