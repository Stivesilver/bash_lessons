<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid   = $ds->safeGet('stdrefid');
	$tsRefID    = $ds->safeGet('tsRefID');
	$editUrl    = CoreUtils::getURL('../../2013/builder/main.php', array('dskey' => $dskey, 'drefid' => 10));
	$edit       = new editClass("edit1", 0);

	$edit->title      = 'Student Evaluation Builder';
	$edit->finishURL  = '';
	$edit->topButtons = true;
	$edit->saveAndAdd = false;
	$edit->saveLocal  = false;

	$edit->addGroup("Builder Settings");

	$edit->addControl(FFSelect::factory('Report Type')
			->sql("
				SELECT essrtrefid ,essrtdescription
			      FROM webset.es_statedef_reporttype
	             WHERE screfid = " . VNDState::factory()->id . "
	               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	             ORDER BY seq_ord,essrtdescription desc
                ")
			->name('reptypes')
		);

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

	#Evaluation Blocks
	$edit->addGroup('Evaluation Blocks');

	$blocks = array(
		101 => 'Student Demographics',
		102 => 'Evaluation Team Information',
		103 => 'Summary Of Findings/Adverse Effects',
		104 => 'Criteria',
		105 => 'Eligibility Determination'
	);

	$edit->addControl('Evaluation Blocks', 'select_check')
		->name('blocks')
		->data($blocks)
		->selectAll()
		->breakRow();

	#Evaluation Forms
	$edit->addGroup('Evaluation Forms');

	$SQL = "
		SELECT sfrefid,
               form_name
          FROM webset.std_forms_xml std
               INNER JOIN webset.statedef_forms_xml stt ON std.frefid = stt.frefid
               INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.MFCpRefId
         WHERE stdrefid = $tsRefID
         ORDER BY std.lastupdate desc, sfrefid
		";

	$eForms = db::execSQL($SQL)->assocAll();
	$count   = count($eForms);

	for ($i = 0; $i < $count; $i++) {
		$key = $eForms[$i]['sfrefid'];
		$forms[$key] = $eForms[$i]['form_name'];
	}

	$edit->addControl('Evaluation Forms', 'select_check')
		->name('forms')
		->data($forms)
		->breakRow();

	#Builder Generator File
	$edit->addControl('Generator', 'hidden')
		->name('gen_file')
		->value(CoreUtils::getURL('builder_gen.ajax.php', array('dskey' => $dskey)));

	$edit->addButton('Build IEP')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildIEP()');
	
	$edit->addButton(FFIDEAArchiveIEPButton::factory())
		->name('btn_archive')
		->onClick('archiveIEP()');

	$edit->printEdit();

	io::jsVar('dskey', $dskey);

?>

<script type="text/javascript">

	function buildIEP() {
		url = api.url($('#gen_file').val());
		url = api.url(url, {'idReportType': $('#reptypes').val()});
		url = api.url(url, {'IEPDate': $('#iepdate').val()});
		url = api.url(url, {'cont': $('#cont').val()});
		url = api.url(url, {'phn': $('#phn').val()});
		url = api.url(url, {'email': $('#email').val()});
		url = api.url(url, {'format': $('#print_formt').val()});
		url = api.url(url, {'str': $('#blocks').val() != '' ? $('#blocks').val() + ',' : ''});
		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''});
		url = api.url(url, {'dskey': dskey});
		url = api.url(url, {'iepdone': 'yes'});
		win = api.ajax.process(ProcessType.REPORT, url);
		win.addEventListener(ObjectEvent.COMPLETE, IEPDone);
	}

	function archiveIEP() {
		$("#btn_build").attr("disabled", true);
		$("#btn_archive").attr("disabled", true);
		$("#btn_back").attr("disabled", true);
		url = api.url('builder_save.php');
		url = api.url(url, {'dskey': dskey})
		url = api.url(url, {'ReportType': $('#reptypes option:selected').text()})
		url = api.url(url, {'IEPDate': $('#iepdate').val()})
		url = api.url(url, {'RefID': 0})
		url = api.url(url, {'f_str': $('#forms').val() != '' ? $('#forms').val() + ',' : ''})
		api.goto(url);
	}

	function IEPDone() {
		$("#btn_archive").attr("disabled", false);
		$("#btn_archive_top").attr("disabled", false);
	}
</script>
