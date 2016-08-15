<?php
	Security::init();

	$hisrefid = io::geti('hisrefid');

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Upload Document';
	$edit->setSourceTable('webset.std_fif_forms', 'sfrefid');
	$edit->addGroup('Document Information');

	$edit->addControl("Document Title")
		->sqlField('uploaded_title')
		->width('80%')
		->req();

	$edit->addControl(FFFileUpload::factory()->displayFilePath())
		->name('upload_control_file')
		->onChange('docContentToDB();')
		->req();

	$edit->addControl(FFInput::factory())
		->caption('Document File Name')
		->hide(true)
		->sqlField('uploaded_filename')
		->name('uploaded_filename');

	$edit->addControl(FFTextArea::factory())
		->caption('Document Data')
		->hide(true)
		->sqlField('uploaded_content')
		->name('uploaded_content');

	$edit->addControl('Process ID', 'hidden')
		->sqlField('hisrefid')
		->name('hisrefid')
		->value($hisrefid);

	$edit->cancelURL = 'javascript:api.window.destroy();';
	$edit->finishURL = 'javascript:api.window.dispatchEvent("form_saved");api.window.destroy();';

	$edit->saveAndAdd = false;
	$edit->topButtons = false;

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->printEdit();
?>
<script type="text/javascript">
	function docContentToDB() {
		url = api.url('form_doc_content.ajax.php');
		api.ajax.post(
			url,
			{'upload_control_file': $('#upload_control_file').val()},
			function (answer) {
				$('#uploaded_content').val(answer.uploaded_content);
				$('#uploaded_filename').val(answer.uploaded_filename);
			}
		);

	}
</script>
