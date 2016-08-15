<?php

	Security::init();

	$msmRefId = io::geti('msm_refid');

	$list = new ListClass();

	$list->setMasterRecordID($msmRefId);

	$list->editURL         = 'javascript:checkForm(AF_REFID)';
	$list->multipleEdit    = true;
	$list->deleteTableName = 'webset.med_std_form';
	$list->deleteKeyField  = 'msf_refid';

	$list->SQL     = "
		SELECT *
		  FROM webset.med_std_form
		 WHERE msm_refid = $msmRefId
	";

	$list->addColumn('Document Type')
		->sqlField('msf_doc_type');

	$list->addColumn('Date Signed')
		->sqlField('msf_date_signed');

	$list->addColumn('Date Uploaded')
		->sqlField('msf_date_uploaded');

	$list->addColumn('File Name')
		->sqlField('msf_file_name');

	$list->addButton('Upload Consent \ Revoke Documentation Files')
		->onClick('uploadForm()');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

	io::jsVar('msm_refid', $msmRefId);

?>

<script type="text/javascript">

	function uploadForm() {
		var url = api.url('forms_add.php',
			{'msm_refid': msm_refid}
		);
		var win = api.goto(url);
	}

	function checkForm(msm_refid) {
		api.ajax.process(UIProcessBoxType.REPORT, api.url('form_check.ajax.php'), {'msm_refid' : msm_refid});
	}

</script>