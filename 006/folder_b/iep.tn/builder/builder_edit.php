<?php

	Security::init();

	$dskey = io::get('dskey');
	$typeBlock = io::geti('idBlock');
	$ds = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdrefid = $ds->safeGet('stdrefid');
	$set_ini = IDEAFormat::getIniOptions();
	$doc = IDEADocumentType::factory($typeBlock);

	$edit = new EditClass('edit1', 0);

	$edit->title = $set_ini['iep_title'] . ' Builder';
	$edit->finishURL = '';
	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal = false;

	$edit->addControl(FFIDEAIEPTypes::factory($set_ini['iep_title'] . ' Types'))
		->name('ieptypes');

	$edit->addGroup($set_ini['iep_title'] . ' Blocks');
	$edit->addControl($set_ini['iep_title'] . ' Blocks', 'select_check')
		->data($doc->getBlocksKeyedArray())
		->breakRow()
		->selectAll()
		->name('blocks');

	$edit->addControl('nameFile', 'hidden')
		->name('nameFile');

	$edit->addButton('Build ' . $set_ini['iep_title'])
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
			array('dskey' => $dskey, 'doc' => IDEABlockBuilder::TN)
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
