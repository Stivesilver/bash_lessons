<?php

	Security::init();

	$dskey = io::get('dskey');
	$typeBlock = io::geti('idBlock');
	$ds = DataStorage::factory($dskey, true);
	$evalpoc_id = $ds->safeGet('evalproc_id');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdrefid = $ds->safeGet('stdrefid');
	$edit = new EditClass('edit1', 0);

	$doc = IDEADocumentType::factory($typeBlock);
	$title = $doc->getTitle();

	$edit->title = $title . ' Builder';
	$edit->finishURL = '';
	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal = false;

	$edit->addControl('Date of Report', 'date')
		->name('report_date')
		->req();

	$edit->addControl(FFCheckBox::factory('Draft'))
		->name('draft');

	$edit->addGroup($title . ' Blocks');
	$edit->addControl($title . ' Blocks', 'select_check')
		->data($doc->getBlocksKeyedArray())
		->breakRow()
		->selectAll()
		->name('blocks');

	$edit->addControl('nameFile', 'hidden')
		->name('nameFile');

	$edit->addButton('Build')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildEval()');

	$edit->addButton(FFIDEAArchiveEvalButton::factory())
		->name('btn_archive')
		->onClick('archiveEval()');

	$edit->printEdit();

	io::jsVar('stdrefid', $tsRefID);
	io::jsVar('evalpoc_id', $evalpoc_id);
	io::jsVar('typeBlock', $typeBlock);
	io::jsVar(
		'url',
		CoreUtils::getURL(
			'./builder_list.php',
			array('dskey' => $dskey, 'doc' => $typeBlock)
		)
	);
?>

<script type="text/javascript">

	function buildEval() {
		if ($('#report_date').val() == '') {
			api.alert('Please specify Date of Report');
			return false;
		}
		if ($('#blocks').val() == '') {
			api.alert('Please select Blocks');
			return false;
		}
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('add_file.ajax.php'),
			{
				'blocks': $('#blocks').val(),
				'archive': 0,
				'tsRefID': stdrefid,
				'evalpoc_id': evalpoc_id,
				'typeBlock': typeBlock,
				'report_date': $('#report_date').val(),
				'draft': $('#draft').prop('checked') ? 'yes' : 'no'
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

	function archiveEval() {
		if ($('#report_date').val() == '') {
			api.alert('Please specify Date of Report');
			return false;
		}
		api.ajax.post(
			api.url('add_file.ajax.php'),
			{
				'archive': 1,
				'nameFile': $('#nameFile').val(),
				'blocks': $('#blocks').val(),
				'stdrefid': stdrefid,
				'report_date': $('#report_date').val(),
				'typeBlock': typeBlock
			},
			function (answer) {
				if (answer.finish == 1) {
					api.goto(url);
				}
			}
		)
	}

</script>
