<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Upload Document';
	$edit->setSourceTable('webset.es_std_evalproc_forms', 'frefid');
	$edit->addGroup('Document Information');

	$edit->addControl("Document Title")
		->sqlField('uploaded_title')
		->width('80%')
		->req();

	$edit->addControl(FFFileUpload::factory()->displayFilePath())
		->name('uploaded_filename')
		->path('/sec_disk/evalsum/')
		->sqlField('uploaded_filename')
		->req();

	$edit->cancelURL = 'javascript:api.window.destroy();';
	$edit->finishURL = 'javascript:api.window.dispatchEvent("form_saved");api.window.destroy();';

	$edit->saveAndAdd = false;
	$edit->topButtons = false;

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('evalproc_id', 'hidden')->value($evalproc_id)->sqlField('evalproc_id');

	$edit->printEdit();
?>
