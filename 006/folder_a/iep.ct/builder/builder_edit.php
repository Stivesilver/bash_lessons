<?php

	Security::init();

	$dskey = io::get('dskey');
	$typeBlock = io::geti('idBlock');
	$ds = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdrefid = $ds->safeGet('stdrefid');
	$doc = IDEADocumentType::factory($typeBlock);

	$edit = new EditClass('edit1', 0);

	$edit->title = 'IEP Builder';
	$edit->finishURL = '';
	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal = false;

	$edit->addControl(FFIDEAIEPTypes::factory())
		->name('ieptypes');

	#Blank DESE Form
	$edit->addControl('Blank IEP Form', 'protected')
		->append(
			UIAnchor::factory('Preview')
				->onClick('api.ajax.process(UIProcessBoxType.PROCESS, "blank_form.ajax.php")')
				->toHTML()
		);

	$edit->addGroup('Optional IEP Blocks');
	$edit->addControl('Optional IEP Blocks', 'select_check')
		->data(IDEADocumentType::factory(IDEABlockBuilder::CT_IEP_OPTIONAL)->getBlocksKeyedArray())
		->breakRow()
		->displaySelectAllButton(false)
		->name('opt_blocks');

	$edit->addGroup('IEP Blocks');
	$edit->addControl('IEP Blocks', 'select_check')
		->data($doc->getBlocksKeyedArray())
		->breakRow()
		->selectAll()
		->name('blocks');

	//	$edit->addGroup('IEP Forms');
	//	$edit->addControl('Attachments', 'select_check')
	//		->sql("
	//			SELECT smfcrefid,
	//                   CASE WHEN xml_content IS NULL THEN 'PDF' ELSE 'XML' END || ': ' || MFCDocTitle || ': ' ||  to_char(smfcdate,'MM-DD-YYYY'),
	//                   smfcdate
	//			  FROM webset.std_forms
	//                   INNER JOIN webset.statedef_forms ON webset.std_forms.MFCRefId = webset.statedef_forms.MFCRefId
	//			 WHERE stdrefid = $tsRefID
	//               AND (html_content is Null or html_content='')
	//               AND mfcprefid=18
	//			 ORDER BY 3 desc
	//		")
	//		->breakRow()
	//		->selectAll()
	//		->name('attachments');

	$edit->addControl('nameFile', 'hidden')
		->name('nameFile');

	$edit->addButton('Build IEP')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildIEP()');

	$edit->addButton(FFIDEAArchiveIEPButton::factory())
		->name('btn_archive')
		->onClick('archiveIEP()');

	$edit->printEdit();

	io::jsVar('stdrefid', $tsRefID);
	io::jsVar('stdIEPYear', $stdIEPYear);
	io::jsVar('typeBlock', $typeBlock);
	io::jsVar(
		'url',
		CoreUtils::getURL(
			'/apps/idea/iep/builder_list.php',
			array('dskey' => $dskey, 'doc' => 79)
		)
	);
?>

<script type="text/javascript">

	function buildIEP() {
		if ($('#blocks').val() == '') {
			api.alert('Please select IEP Blocks');
			return false;
		}
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('add_file.ajax.php'),
			{
				'blocks': $('#blocks').val(),
				'opt_blocks': $('#opt_blocks').val(),
				'archive': 0,
				'IEPType': $('#ieptypes').val(),
				'tsRefID': stdrefid,
				'stdIEPYear': stdIEPYear,
				'standartAss': $('#standart_ass').val(),
				'rationale': $('#rationale').val(),
				'typeBlock': typeBlock
			}
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				$('#nameFile').val(e.param.nameFile);
				$('#btn_archive').attr('disabled', false);
				$('#btn_archive_top').attr('disabled', false);
			}
		);
	}

	function archiveIEP() {
		api.ajax.post(
			api.url('add_file.ajax.php'),
			{
				'archive': 1,
				'nameFile': $('#nameFile').val(),
				'IEPType': $('#ieptypes').val(),
				'reason': $('#reason').val(),
				'standart_ass': $('#standart_ass').val(),
				'blocks': $('#blocks').val(),
				'attachments': $('#attachments').val(),
				'stdrefid': stdrefid
			},
			function (answer) {
				if (answer.finish == 1) {
					api.goto(url);
				}
			}
		)
	}

</script>
