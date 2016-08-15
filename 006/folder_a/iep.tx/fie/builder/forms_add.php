<?php

	Security::init();

	$edit = new EditClass('edit1', 0);

	$edit->title      = "Upload Document";
	$edit->saveAndAdd = true;
	$edit->finishURL  = 'javascript:finishUrl()';

	$edit->addGroup('General Information');

	$edit->addControl("Document Description", "edit")
		->value("FIE")
		->size(80)
		->maxlength(100);

	$edit->addControl('Date Uploaded', 'hidden')
		->sqlField('msf_date_uploaded')
		->value(date('Y-m-d H:i:s'));

	$edit->addControl(FFFileUpload::factory()->displayFilePath())
		->name('upload_control_file')
		->onChange('docContentToDB();')
		->req();

	$edit->addControl('Document File Name', 'hidden')
		->sqlField('msf_file_name')
		->name('msf_file_name');

	$edit->addControl('Document Data', 'hidden')
		->sqlField('msf_file_content')
		->name('msf_file_content');

	$edit->printEdit();

	io::jsVar('stdrefid', io::get('stdrefid'));
	io::jsVar('dskey',    $dskey);

?>

<script type="text/javascript">
	function docContentToDB() {
		url = api.url('form_doc_content.ajax.php');
		api.ajax.post(
			url,
			{'upload_control_file': $('#upload_control_file').val()},
			function (answer) {
				$('#msf_file_content').val(answer.uploaded_content);
				$('#msf_file_name').val(answer.uploaded_filename);
			}
		);

	}

	function finishUrl() {
		api.ajax.post(
			api.url('save_file.php'),
			{
				'f_path'  : $('#upload_control_file').val(),
				'f_name'  : $('#msf_file_name').val(),
				'f_cont'  : $('#msf_file_content').val(),
				'stdrefid': stdrefid
			},
			function(answer) {

			}
		);

		var url = api.url('pdf_builder.php',
			{}
		);
		var win = api.goto(url, {'dskey': dskey});
	}

</script>