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

    function checkBlock($tsRefID, $stdIEPYear, $path) {
        $fullpath = SystemCore::$physicalRoot . '/apps/idea' . $path;
        if (file_exists($fullpath) && $path) {
            $a = include($fullpath);
            return $a;
        }
        return true;
    }

    #IEP Blocks Check
    $allblocks = IDEAFormat::getDocBlocks($reptype);
    for ($i = 0; $i < count($allblocks); $i++) {
        if (checkBlock($tsRefID, $stdIEPYear, $allblocks[$i]['iepinclude']) == 1) {
            $blocks[$allblocks[$i]['iepnum']] = $allblocks[$i]['iepdesc'];
        }
    }

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
        ->onChange("api.goto(api.url('iep_main.php', {'reptype': this.value, 'dskey': '" . $dskey . "'}))");

    #IEP Types
    $edit->addControl(FFIDEAIEPTypes::factory())
        ->name('ieptypes');

    #IEP Date
    $edit->addControl('Date', 'date')
        ->name('iepdate')
        ->value(date('Y-m-d'));

    $edit->addGroup('IEP Blocks');

    #IEP Blocks
    $edit->addControl('IEP Blocks', 'select_check')
        ->name('iepblocks')
        ->data($blocks)
        ->selectAll()
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

	$edit->addButton('Check IEP')
		->onClick('checkIEP()')
		->showIf('iepdate', '2000-01-01');

    $edit->addButton('Build IEP')
        ->name('btn_build')
        ->css('width', '120px')
        ->onClick('buildIEP()');

    $edit->addButton(FFIDEAArchiveIEPButton::factory())
        ->name('btn_archive')
        ->onClick('archiveIEP()');


    $edit->cancelURL = CoreUtils::getURL('iep_builder.php', array('dskey' => $dskey));

	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();

    $edit->topButtons = true;
    $edit->saveAndAdd = false;
    $edit->saveLocal = false;
    $edit->printEdit();
?>
<script type="text/javascript">
    function buildIEP() {
        url = api.url($('#gen_file').val());
        url = api.url(url, {'IEPType': $('#ieptypes option:selected').text()});
        url = api.url(url, {'ReportType': $('#reptypes').val()});
        url = api.url(url, {'IEPDate': $('#iepdate').val()});
        url = api.url(url, {'str': $('#iepblocks').val() != '' ? $('#iepblocks').val() + ',' : ''});
        url = api.url(url, {'dskey': $('#dskey').val()});
        url = api.url(url, {'iepdone': 'yes'});
        win = api.ajax.process(ProcessType.REPORT, url);
        win.addEventListener(ObjectEvent.COMPLETE, IEPDone);
    }

    function checkIEP() {
        url = api.url($('#gen_file').val());
        url = api.url(url, {'IEPType': $('#ieptypes option:selected').text()});
        url = api.url(url, {'ReportType': $('#reptypes').val()});
        url = api.url(url, {'IEPDate': $('#iepdate').val()});
        url = api.url(url, {'str': $('#iepblocks').val() != '' ? $('#iepblocks').val() + ',' : ''});
        url = api.url(url, {'dskey': $('#dskey').val()});
        url = api.url(url, {'iepdone': 'yes'});
		var wnd = api.window.open('Check IEP', url);
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('user_selected', onEvent);
        wnd.show();
    }

    function IEPDone() {
        $("#btn_archive").attr("disabled", false);
        $("#btn_archive_top").attr("disabled", false);

    }

	function archiveIEP() {
        $("#btn_build").attr("disabled", true);
        $("#btn_archive").attr("disabled", true);
        $("#btn_back").attr("disabled", true);
        url = api.url($('#gen_file').val());
        url = api.url(url, {'IEPType': $('#ieptypes option:selected').text()});
        url = api.url(url, {'ReportType': $('#reptypes').val()});
        url = api.url(url, {'IEPDate': $('#iepdate').val()});
        url = api.url(url, {'str': $('#iepblocks').val() != '' ? $('#iepblocks').val() + ',' : ''});
        url = api.url(url, {'dskey': $('#dskey').val()});
        url = api.url(url, {'mode': 'archive'});
        win = api.ajax.post(url, {}, IEPArchived);
    }

	function IEPArchived(e) {
		url = api.url('iep_save.php');
        url = api.url(url, {'dskey': $('#dskey').val()})
        url = api.url(url, {'IEPType': $('#ieptypes').val()})
        url = api.url(url, {'ReportType': $('#reptypes').val()})
        url = api.url(url, {'IEPDate': $('#iepdate').val()})
        api.goto(url);
    }
</script>
