<?php
    Security::init();

    require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $SQL = "
        SELECT std.pdf_refid,
               std.formrefid,
               mfcrefid
          FROM webset.std_spconsid std
               INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
               LEFT OUTER JOIN webset.statedef_forms form ON form.mfcrefid = ans.formrefid
         WHERE sscmrefid = " . io::geti('spconsid') . "
    ";
    $spconsid = db::execSQL($SQL)->assoc();

    if ($spconsid['pdf_refid'] > 0) {
        $form = db::execSQL("
            SELECT mfcdoctitle,
                   form_xml,
                   xml_field_links,
                   fdf_content,
                   forms.mfcrefid,
                   smfcfilename,
                   archived
              FROM webset.std_forms forms
                   INNER JOIN webset.statedef_forms state ON forms.mfcrefid = state.mfcrefid
                   INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
             WHERE smfcrefid = " . $spconsid['pdf_refid'] . "
        ")->assoc();
        $xml_title = $form['mfcdoctitle'];
        $xml_content = base64_decode($form['form_xml']);
        $fdf_content = base64_decode($form['fdf_content']);
        $mfcrefid = $form['mfcrefid'];
        $archived = ($form['archived'] == 'Y');
        $smfcfilename = $form['smfcfilename'];
        $RefID = $spconsid['pdf_refid'];
    } else {
        $form = db::execSQL("
            SELECT mfcdoctitle,
                   form_xml,
                   xml_field_links
              FROM webset.statedef_forms state
                   INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
              WHERE mfcrefid = " . $spconsid['mfcrefid'] . "
        ")->assoc();
        $xml_title = $form['mfcdoctitle'];
        $xml_content = base64_decode($form['form_xml']);
        $fdf_content = IDEAFormDefaults::factory($tsRefID)
            ->addValues(array('strUrlEnd' => '?tsRefID=' . $tsRefID . '&mfcrefid=' . $spconsid['mfcrefid']))
            ->getFDF();
        $mfcrefid = $spconsid['mfcrefid'];
        $archived = false;
        $smfcfilename = 'Form_' . $tsRefID . '_' . date('mdhis') . '.fdf';
        $RefID = 0;
    }

    //PROCESS XML DOCUMENT
    $doc = new xmlDoc();
    $doc->edit_mode = 'yes';
    $doc->edit_prefix = 'constr_';
    $doc->border_color = 'silver';
    $doc->includeStyle = 'no';

    $xml_content = IDEAFormPDF::replace_id($xml_content, base64_decode($form['xml_field_links']));
    $xml_values = IDEAFormPDF::fdf2xml($fdf_content, $xml_content);

    $mergedDocData = $doc->xml_merge($xml_content, $xml_values);
    $doc->xml_data = $mergedDocData;
    $html_result = $doc->getHtml();

    $edit = new editClass('edit1', $RefID);

    $edit->title = $xml_title;

    $edit->setSourceTable('webset.std_forms', 'smfcrefid');

    $edit->addGroup('General Information');
    $edit->addControl('', 'protected')
        ->prepend($html_result);

    $edit->addGroup('Update Information', true);
    $edit->addControl('', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('', 'hidden')->value($mfcrefid)->sqlField('mfcrefid')->name('mfcrefid');
    $edit->addControl('', 'hidden')->value($smfcfilename)->sqlField('smfcfilename')->name('smfcfilename');
    $edit->addControl('', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl('', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

    $edit->saveAndEdit = true;
    $edit->saveAndAdd = false;
    $edit->topButtons = true;

    $edit->saveLocal = false;
    $edit->firstCellWidth = '0%';

    $edit->addButton('Print')
        ->css('width', '120px')
        ->onClick('printForm()');

    $edit->finishURL = CoreUtils::getURL('srv_spconsid_form_save.php', $_GET);
    $edit->saveURL = CoreUtils::getURL('srv_spconsid_form_save.php', $_GET);
    $edit->cancelURL = 'javascript:api.window.destroy();';

    if ($archived) {
        $edit->getButton(EditClassButton::SAVE_AND_FINISH)->disabled(true);
        $edit->getButton(EditClassButton::SAVE_AND_EDIT)->disabled(true);
    }

    $edit->printEdit();
?>
<script type="text/javascript">
    function printForm() {
        var wnd = api.window.open('Print', api.url('/apps/idea/iep/evalforms/frm_print_dialog.php'));
        wnd.resize(400, 300);
        wnd.center();
        wnd.addEventListener('format_selected', onEvent);
        wnd.show();
    }

    function onEvent(e) {
        var format = e.param.format;
        var options = e.param.options;
        var d = new Date();
        var wname = 'printWin' + d.getTime()
        $('#edit1').attr('target', wname);
        $('#edit1').attr('action', api.url('/apps/idea/iep/evalforms/frm_print.php', {'format': format,
            'options': options}));
        $('#edit1').get(0).submit();
    }
</script>
