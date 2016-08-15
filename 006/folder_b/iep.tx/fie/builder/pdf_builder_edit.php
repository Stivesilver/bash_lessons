<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdrefid   = $ds->safeGet('stdrefid');
	$edit       = new EditClass('edit1', 0);

	$edit->title      = 'FIE Builder';
	$edit->finishURL  = '';
	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal  = false;

	$edit->addGroup('Builder Settings');

	$edit->addControl('Reason for referral', 'edit')
		->name('reason');

	$edit->addControl(
		FFSwitchYN::factory('Standard Assessment')
			->name('standart_ass')
	)
	->value('Y');

	$edit->addControl('Please explain the rationale:', 'edit')
		->name('rationale')
		->showIf('standart_ass', 'N');

	$edit->addControl('FIE Meeting Date', 'date')
		->value(date('m-d-Y'))
		->name('meeting_date');

	$edit->addGroup('FIE Blocks');

	//$a = new IDEABlockFIE();
	//io::trace($a);

	$edit->addControl('FIE Blocks', 'select_check')
		->data(IDEABlockBuilder::create(IDEABlockBuilder::FIE)->getBlocks())
		->breakRow()
		->selectAll()
		->name('fie_blocks');

	$edit->addGroup('FIE Forms');

	$edit->addControl('Attachments', 'select_check')
		->sql("
				SELECT smfcrefid,
	                   CASE WHEN xml_content IS NULL THEN 'PDF' ELSE 'XML' END || ': ' || MFCDocTitle || ': ' ||  to_char(smfcdate,'MM-DD-YYYY'),
	                   smfcdate
				  FROM webset.std_forms
	                   INNER JOIN webset.statedef_forms ON webset.std_forms.MFCRefId = webset.statedef_forms.MFCRefId
				 WHERE stdrefid = $tsRefID
	               AND (html_content is Null or html_content='')
	               AND mfcprefid=18
				 ORDER BY 3 desc
			")
		->breakRow()
		->selectAll()
		->name('attachments');

	$edit->addControl('nameFile', 'hidden')
		->name('nameFile');

	$edit->addButton('Build IEP')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildIEP()');

	$edit->addButton('Archive')
		->value('Archive')
		->name('btn_archive')
		->disabled(true)
		->css('width', '120px')
		->onClick('archiveIEP()');

	$edit->printEdit();

	io::jsVar('stdrefid',   $tsRefID);
	io::jsVar('stdIEPYear', $stdIEPYear);
	io::jsVar(
		'url',
		CoreUtils::getURL(
			'pdf_builder.php',
			array('dskey' => $dskey)
		)
	);
?>

<script type="text/javascript">

	function buildIEP() {
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('add_file.ajax.php'),
			{
				'fie_blocks'  : $('#fie_blocks').val(),
				'archive'     : 0,
				'fie_date'    : $('#meeting_date').val(),
				'tsRefID'     : stdrefid,
				'reason'      : $('#reason').val(),
				'stdIEPYear'  : stdIEPYear,
				'standartAss' : $('#standart_ass').val(),
				'rationale'   : $('#rationale').val()
			}
		).addEventListener(
			ObjectEvent.COMPLETE,
			function(e) {
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
				'archive'      : 1,
				'nameFile'     : $('#nameFile').val(),
				'reason'       : $('#reason').val(),
				'standart_ass' : $('#standart_ass').val(),
				'meeting_date' : $('#meeting_date').val(),
				'fie_blocks'   : $('#fie_blocks').val(),
				'attachments'  : $('#attachments').val(),
				'stdrefid'     : stdrefid
			},
			function(answer) {
				if (answer.finish == 1){
					api.goto(url);
				}
			}
		)
	}

</script>
