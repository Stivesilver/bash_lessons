<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);
	if (io::get('drefid') == '') {
		$doc_type = IDEAFormat::getDocs(true);
	} else {
		$doc_type = IDEADocumentType::factory(io::get('drefid'));
	}
	$drefid = $doc_type->getDocID();

	function checkBlock($tsRefID, $stdIEPYear, $path) {
		$fullpath = SystemCore::$physicalRoot . '/apps/idea' . $path;
		if (file_exists($fullpath) && $path) {
			$a = include($fullpath);
			return $a;
		}
		return true;
	}

	#IEP Blocks Check
	$allblocks = $doc_type->getBlocksArray();
	for ($i = 0; $i < count($allblocks); $i++) {
		if (checkBlock($tsRefID, $stdIEPYear, $allblocks[$i]['iepinclude']) == 1) {
			$blocks[$allblocks[$i]['iepnum']] = $allblocks[$i]['iepdesc'];
		}
	}

	$edit = new EditClass('edit1', 0);
	$edit->title = $doc_type->getTitle() . ' Builder';

	$edit->addGroup('Builder Settings');

	#Report Type
	$edit->addControl('Report Type', 'select')
		->name('reptypes')
		->value($drefid)
		->data(array($drefid => $doc_type->getTitle()))
		->onChange("api.goto(api.url('main.php', {'reptype': this.value, 'dskey': '" . $dskey . "'}))");

	#IEP Types
	$edit->addControl(FFIDEAIEPTypes::factory())
		->name('ieptypes')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'builder_ieptype', 0));

	#IEP Date
	$edit->addControl('Date', 'date')
		->name('iepdate')
		->value(date('Y-m-d'));

	#Contact
	$edit->addControl('Contact')
		->name('cont')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'builder_contact', 0));

	#Phone
	$edit->addControl('Phone')
		->name('phn')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'builder_phone', 0));

	#Email
	$edit->addControl('Email')
		->name('email')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'builder_email', 0))
		->css('width', '250px');

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

	$edit->addGroup('IEP Blocks');

	#IEP Blocks
	$edit->addControl('IEP Blocks', 'select_check')
		->name('iepblocks')
		->data($blocks)
		->selectAll()
		->breakRow();

	$edit->addGroup('Forms');

	#Forms
	$edit->addControl('Forms', 'select_check')
		->name('forms')
		->sql("
			SELECT sfrefid,
				   form_name || ' (' || TO_CHAR(std.lastupdate, 'mm-dd-yyyy') || ' by ' || std.lastuser || ')'
			  FROM webset.std_forms_xml std
				   INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
				   INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.MFCpRefId
			 WHERE stdrefid=" . $tsRefID . "
			 ORDER BY std.lastupdate desc, sfrefid
        ")
		->breakRow();

	#Sp Ed Student ID
	$edit->addControl('Student ID', 'hidden')
		->name('tsRefID')
		->value($tsRefID);

	#Doc Type ID
	$edit->addControl('Doc Type ID', 'hidden')
		->name('drefid')
		->value(io::get('drefid'));

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

	$edit->cancelURL = CoreUtils::getURL('builder.php', array('dskey' => $dskey, 'drefid' => io::get('drefid')));
	$edit->finishURL = '';

	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal = false;
	$edit->printEdit();
?>
<script type="text/javascript">
	function buildIEP() {
		url = api.url($('#gen_file').val());
		url = api.url(url, {'IEPType': $('#ieptypes').val()});
		url = api.url(url, {'idReportType': $('#reptypes').val()});
		url = api.url(url, {'IEPDate': $('#iepdate').val()});
		url = api.url(url, {'cont': $('#cont').val()});
		url = api.url(url, {'phn': $('#phn').val()});
		url = api.url(url, {'email': $('#email').val()});
		url = api.url(url, {'format': $('#print_formt').val()});
		url = api.url(url, {'str': $('#iepblocks').val() != '' ? $('#iepblocks').val() + ',' : ''});
		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''});
		url = api.url(url, {'dskey': $('#dskey').val()});
		url = api.url(url, {'iepdone': 'yes'});
		win = api.ajax.process(ProcessType.REPORT, url);
		win.addEventListener(ObjectEvent.COMPLETE, IEPDone);
	}

	function archiveIEP() {
		$("#btn_build").attr("disabled", true);
		$("#btn_archive").attr("disabled", true);
		$("#btn_back").attr("disabled", true);
		url = api.url('save.php');
		url = api.url(url, {'dskey': $('#dskey').val()})
		url = api.url(url, {'IEPType': $('#ieptypes').val()})
		url = api.url(url, {'ReportType': $('#reptypes').val()})
		url = api.url(url, {'IEPDate': $('#iepdate').val()})
		url = api.url(url, {'drefid': $('#drefid').val()})
		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''})
		api.goto(url);
	}

	function IEPDone() {
		$("#btn_archive").attr("disabled", false);
		$("#btn_archive_top").attr("disabled", false);
	}
</script>
