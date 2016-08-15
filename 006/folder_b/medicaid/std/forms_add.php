<?php

	Security::init();

	$msmRefId = io::geti('msm_refid');
	$edit     = new EditClass('edit1', 0);

	$edit->title      = 'Upload File';
	$edit->saveAndAdd = true;
	$edit->finishURL  = 'javascript:finishUrl()';

	$edit->setSourceTable('webset.med_std_form', 'msf_refid');
	$edit->addGroup('General Information');

	$edit->addControl('Document Type', 'select')
		->sqlField('msf_doc_type')
		->data(array(
			'Consent'  => 'Consent',
			'Revoke'   => 'Revoke',
			'Not used' => 'Not used'
		))
		->value('Consent');

	$edit->addControl('Date Signed', 'date')
		->sqlField('msf_date_signed')
		->value(date('Y-m-d H:i:s'));

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

	$edit->addControl('msmrefid', 'hidden')
		->sqlField('msm_refid')
		->value($msmRefId);

	$edit->printEdit();

	io::jsVar('msm_refid', $msmRefId);

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
		var url = api.url('forms_list.php',
			{'msm_refid': msm_refid}
		);
		var win = api.goto(url);
	}

</script>