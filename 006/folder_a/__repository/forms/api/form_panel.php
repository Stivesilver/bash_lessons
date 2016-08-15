<?php
	Security::init();

	/** @noinspection PhpIncludeInspection */
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$fkey = io::get('fkey', true);
	$fds = DataStorage::factory($fkey);
	//$form = IDEAForm::factory($fkey);

	//PROCESS XML DOCUMENT
	$doc = new xmlDoc();

	/** @noinspection PhpUndefinedFieldInspection */
	$doc->edit_mode = 'yes';
	/** @noinspection PhpUndefinedFieldInspection */
	$doc->edit_prefix = 'form_';
	/** @noinspection PhpUndefinedFieldInspection */
	$doc->border_color = 'silver';
	/** @noinspection PhpUndefinedFieldInspection */
	$doc->includeStyle = 'no';

	$title = $fds->get('title');
	$template = $fds->get('template');
	$template_pdf = $fds->get('template_pdf');
	$values = $fds->get('values');
	$archived = $fds->get('archived');
	//$lastuser = $fds->get('lastuser');
	//$lastupdate = $fds->get('lastupdate');
	$finish_url = $fds->get('url_finish');
	$save_url = $fds->get('url_save');
	$save_func = $fds->get('save_func');
	$cancel_url = $fds->get('url_cancel');
	$add_option = $fds->get('add_option');
	$controls = unserialize($fds->get('controls'));

	$js = $fds->get('js');
	io::js($js);
	/** @noinspection PhpUndefinedFieldInspection */
	$doc->xml_data = $doc->xml_merge($template, $values);

	$edit = new EditClass('edit1', $fkey);

	$edit->title = $title;

	$edit->addGroup('General Information');

	foreach ($controls as $control) {
		$edit->addControl($control['name'], $control['type'])->id($control['id'])->name($control['id'])->value($control['value'])->width('100%');
	}
	$edit->addHTML($doc->getHtml());

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')
		->value(SystemCore::$userUID)
		->name('lastuser');

	$edit->addControl('Last Update', 'protected')
		->value(date('m-d-Y H:i:s'))
		->name('lastupdate');

	$edit->firstCellWidth = '0%';

	$edit->addButton(
		empty($template_pdf)
			?
			FFMenuButton::factory('Print')
				->iconsSize(32)
				->leftIcon('./img/printer.png')
				->addItem('PDF', 'printForm("pdf", "' . $fkey . '")', './img/PDF.png')
				->addItem('HTML', 'printForm("html", "' . $fkey . '")', './img/HTML.png')

			:
			FFMenuButton::factory('Print')
				->iconsSize(32)
				->leftIcon('./img/printer.png')
				->addItem('PDF (Normal)', 'printForm("pdf_normal", "' . $fkey . '")', './img/PDF.png')
				->addItem('PDF (XML)', 'printForm("pdf", "' . $fkey . '")', './img/PDF.png')
				->addItem('HTML', 'printForm("html", "' . $fkey . '")', './img/HTML.png')
	);

	if ($fds->get('populate_button')) {
		$button = new IDEAFormPopulate($fkey);
		$editButton = $button->getPopulateButton();
		$edit->addButton($editButton);
	}

	$edit->setPresaveCallback($save_func, $save_url);
	$edit->finishURL = $finish_url;
	$edit->cancelURL = $cancel_url;
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = $add_option;
	$edit->topButtons = true;

	if ($archived) {
		$edit->getButton(EditClassButton::SAVE_AND_FINISH)->disabled(true);
		$edit->getButton(EditClassButton::SAVE_AND_EDIT)->disabled(true);
	}

	$edit->printEdit();
?>
<script type="text/javascript">
	function printForm(format, fkey) {
		var url = api.url('form_print.ajax.php', {'format': format, 'RefID': fkey});
		var vars = EditClass.get().getFormData();
		api.ajax.process(UIProcessBoxType.REPORT, url, vars);
	}
</script>
