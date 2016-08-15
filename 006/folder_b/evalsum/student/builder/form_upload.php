<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Upload Document';
	$edit->setSourceTable('', '');
	$edit->addGroup('Document Information');

	$edit->addControl("Document Title")
		->name('uploaded_title')
		->width('80%')
		->req();

	$edit->addControl(FFFileUpload::factory()->displayFilePath())
		->name('uploaded_filename')
		->req();

	$edit->cancelURL = 'javascript:api.window.destroy();';
	$edit->finishURL = 'javascript:api.window.dispatchEvent("form_saved");api.window.destroy();';

	$edit->setPresaveCallback('save_upload', 'save_upload.inc.php', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->topButtons = false;


	$edit->printEdit();
?>
