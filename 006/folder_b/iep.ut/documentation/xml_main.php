<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);
	$reptype = io::geti('reptype') > 0 ? io::geti('reptype') : db::execSQL("
                                                                    SELECT drefid
                                                                      FROM webset.sped_doctype
                                                                     WHERE setrefid = " . IDEAFormat::get('id') . "
                                                                       AND defaultdoc = 'Y'
                                                                ")->getOne();

	$edit = new EditClass('edit1', 0);
	$edit->title = 'IEP Builder';

	$edit->addGroup('Builder Settings');

	#Report Type
	$edit->addControl('Report Type', 'select')
		->name('reptypes')
		->value($reptype)
		->sql("
            SELECT drefid,
                   doctype
              FROM webset.sped_doctype
             WHERE setrefid = " . IDEAFormat::get('id') . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY seqnum
        ")
		->onChange("api.goto(api.url('xml_main.php', {'reptype': this.value, 'dskey': '" . $dskey . "'}))");

	#IEP Types
	$edit->addControl(FFIDEAIEPTypes::factory())
		->name('ieptypes');

	#IEP Date
	$edit->addControl('Date', 'date')
		->name('iepdate')
		->value(date('Y-m-d'));

	#Headers Option
	$edit->addControl(FFCheckBox::factory('Headers Only'))
		->name('headers_only')
		->onChange('headersSelect()');

	#Headers ID container
	$edit->addControl('Headers', 'hidden')
		->name('headers');

	#Progress Report Option
	$edit->addControl(FFCheckBox::factory('Progress Report'))
		->name('progresrep');

	#Draft Copy Option
	$edit->addControl(FFCheckBox::factory('Draft'))
		->name('draft');

	#Print Formats
	$edit->addControl('Format', 'select_radio')
		->name('print_formt')
		->value(IDEACore::disParam(38) == 'N' ? 'HTML' : 'PDF')
		->sql("
            SELECT validvalueid,
                   validvalue,
                   CASE validvalue WHEN 'PDF' THEN 'checked' END
              FROM webset.glb_validvalues
             WHERE valuename = 'printFormats'
               AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = 1
               AND validvalue != 'ODT'
             ORDER BY valuename, sequence_number, validvalue
        ");

	#Blank DESE Form
	$edit->addControl('Blank DESE IEP Form', 'protected')
		->append(
			UIAnchor::factory('Preview')
				->onClick('api.ajax.process(UIProcessBoxType.PROCESSING, "xml_dese_form.ajax.php")')
				->toHTML()
		);

	$edit->addGroup('IEP Blocks');

	#IEP Blocks
	$edit->addControl('IEP Blocks', 'select_check')
		->name('iepblocks')
		->data(IDEAStudentBlock::factory($tsRefID, $stdIEPYear)->getStudentBlocks($reptype))
		->selectAll()
		->breakRow();

	$edit->addGroup('Forms');
	#Forms
	$edit->addControl('Forms', 'select_check')
		->name('forms')
		->sql("
            SELECT smfcrefid,
                   mfcdoctitle || ': ' ||  to_char(smfcdate,'MM-DD-YYYY')
              FROM webset.std_forms
                   INNER JOIN webset.statedef_forms ON webset.std_forms.MFCRefId = webset.statedef_forms.MFCRefId
             WHERE stdrefid = " . $tsRefID . "
               AND iepyear = " . $stdIEPYear . "
               AND (html_content is Null or html_content='' or xml_content is not null)
             ORDER BY webset.std_forms.smfcdate desc
        ")
		->breakRow();


	#Sp Ed Student ID
	$edit->addControl('Student ID', 'hidden')
		->name('tsRefID')
		->value($tsRefID);

	#Data Storadge Key
	$edit->addControl('Data Storadge Key', 'hidden')
		->name('dskey')
		->value($dskey);

	#Builder Generator File
	$edit->addControl('Generator', 'hidden')
		->name('gen_file')
		->value(SystemCore::$virtualRoot . IDEAFormat::get('gen_file'));

	$edit->addButton('Build IEP')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildIEP()');

	$edit->addButton(FFIDEAArchiveIEPButton::factory())
		->name('btn_archive')
		->onClick('archiveIEP()');

	$edit->cancelURL = CoreUtils::getURL('xml_builder.php', array('dskey' => $dskey));

	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal  = false;
	$edit->printEdit();

	io::jsVar('tsRefID', $tsRefID);

//	$a = new IDEAStudentMO($tsRefID);
//	$b = $a->getBgbGoals();
//	echo '<pre>';
//	print_r($b);
//	print_r($a->getBgbGoalsMeasures());
//	echo '</pre>';

?>
<script type="text/javascript">
	function buildIEP() {
//		url = api.url($('#gen_file').val());
		var url = $('#gen_file').val();
		var post = {
			'IEPType': $('#ieptypes option:selected').text(),
			'ReportType': $('#reptypes').val(),
			'IEPDate': $('#iepdate').val(),
			'format': $('#print_formt').val(),
			'str': $('#iepblocks').val(),
			'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : '',
			'prog_rep': $('#progresrep').attr('checked') ? 'yes' : 'no',
			'draft': $('#draft').attr('checked') ? 'yes' : 'no',
			'headers': $('#headers_only').attr('checked') == '' ? '' : $('#headers').val(),
			'dskey': $('#dskey').val(),
			'tsRefID': tsRefID,
			'iepdone': 'yes',
			'archive': 0
		}

//		var urlWithParam = api.url(url, post);
		var win = api.ajax.process(UIProcessBoxType.PROCESSING, api.url(url), post);
		win.addEventListener(ObjectEvent.COMPLETE, IEPDone);
//		url = api.url(url, {'IEPType': $('#ieptypes option:selected').text()});
//		url = api.url(url, {'ReportType': $('#reptypes').val()});
//		url = api.url(url, {'IEPDate': $('#iepdate').val()});
//		url = api.url(url, {'format': $('#print_formt').val()});
//		url = api.url(url, {'str': $('#iepblocks').val() != '' ? $('#iepblocks').val() + ',' : ''});
//		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''});
//		url = api.url(url, {'prog_rep': $('#progresrep').attr('checked') ? 'yes' : 'no'});
//		url = api.url(url, {'draft': $('#draft').attr('checked') ? 'yes' : 'no'});
//		url = api.url(url, {'headers': $('#headers_only').attr('checked') == '' ? '' : $('#headers').val()});
//		url = api.url(url, {'dskey': $('#dskey').val()});
//		url = api.url(url, {'iepdone': 'yes'});
//		win = api.ajax.process(UIProcessBoxType.PROCESSING, url);
//		win.addEventListener(ObjectEvent.COMPLETE, IEPDone);
	}

	function archiveIEP() {
		$("#btn_build").attr("disabled", true);
		$("#btn_archive").attr("disabled", true);
		$("#btn_back").attr("disabled", true);
		url = api.url('xml_save.php');
		url = api.url(url, {'dskey': $('#dskey').val()})
		url = api.url(url, {'IEPType': $('#ieptypes').val()})
		url = api.url(url, {'ReportType': $('#reptypes').val()})
		url = api.url(url, {'IEPDate': $('#iepdate').val()})
		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''})
		api.goto(url);
	}

	function headersSelect() {
		if ($("#headers_only").attr('checked') != 'checked') return false;
		if ($('#iepblocks').val() == '') {
			api.alert('Select at least one IEP block');
			$("#headers_only").removeAttr('checked');
			return false;
		}
		var wnd = api.window.open('Select Blocks Please', api.url('xml_headers.php', {'str': $('#iepblocks').val(),
			'rep': $('#reptypes').val(),
			'hdr': $('#headers').val()}));
		wnd.resize(400, 600);
		wnd.center();
		wnd.addEventListener('headersSelected', headersSelected);
		wnd.show();
	}

	function headersSelected(e) {
		$("#headers").val(e.param.str);
	}

	function IEPDone() {
		$("#btn_archive").attr("disabled", false);
		$("#btn_archive_top").attr("disabled", false);

	}

</script>
