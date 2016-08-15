<?php
    Security::init();

    require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    if ($RefID > 0) {
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
             WHERE smfcrefid = " . $RefID . "
        ")->assoc();
        $xml_title = $form['mfcdoctitle'];
        $xml_content = base64_decode($form['form_xml']);
        $fdf_content = base64_decode($form['fdf_content']);
        $mfcrefid = $form['mfcrefid'];
        $archived = ($form['archived'] == 'Y');
        $smfcfilename = $form['smfcfilename'];
    } else {
        $form = db::execSQL("
            SELECT mfcdoctitle,
                   form_xml,
                   xml_field_links,
                   state.file_defaults
              FROM webset.statedef_forms state
                   INNER JOIN webset.statedef_forms_xml xmlform ON state.xmlform_id = xmlform.frefid
              WHERE mfcrefid = " . io::geti('mfcrefid') . "
        ")->assoc();
        $xml_title = $form['mfcdoctitle'];
        $xml_content = base64_decode($form['form_xml']);
        $fdf_content = IDEAFormDefaults::factory($tsRefID)
            ->addValues(array('strUrlEnd' => '?tsRefID=' . $tsRefID . '&mfcrefid=' . io::geti('mfcrefid')))
	        ->addValuesByInclude(str_replace('/applications/webset', '/apps/idea', $form['file_defaults']), array('tsRefID' => $tsRefID))
            ->getFDF();
        $mfcrefid = io::geti('mfcrefid');
        $archived = false;
        $smfcfilename = 'Form_' . $tsRefID . '_' . date('mdhis') . '.fdf';
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
    $edit->addHTML($html_result);

    $edit->addGroup('Update Information', true);
    $edit->addControl('', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('', 'hidden')->value($mfcrefid)->sqlField('mfcrefid')->name('mfcrefid');
    $edit->addControl('', 'hidden')->value($smfcfilename)->sqlField('smfcfilename')->name('smfcfilename');
    $edit->addControl('', 'hidden')->value($tsRefID)->name('tsRefID')->sqlField('stdrefid');
    $edit->addControl('', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

    $edit->saveAndEdit = true;
    $edit->saveAndAdd = false;
    $edit->topButtons = true;

    $edit->saveLocal = false;
    $edit->firstCellWidth = '0%';

    $printButton = FFMenuButton::factory('Print')
        ->iconsSize(32)
        ->leftIcon('./img/printer.png');

    if (!SystemCore::$isTablet) $printButton->addItem('PDF (Normal)', 'printForm("pdf")', './img/PDF.png');
    $printButton->addItem('PDF (XML)', 'printForm("xpdf")', './img/PDF.png');
    $printButton->addItem('HTML', 'printForm("html")', './img/HTML.png');

    $edit->addButton($printButton);

    $edit->finishURL = CoreUtils::getURL('frm_xmlsave.php', $_GET);
    $edit->saveURL = CoreUtils::getURL('frm_xmlsave.php', $_GET);
    $edit->cancelURL = CoreUtils::getURL(strstr($_SERVER['HTTP_REFERER'], 'frm_main.php') ? 'frm_main.php' : 'frm_add.php', $_GET);

    if ($archived) {
        $edit->getButton(EditClassButton::SAVE_AND_FINISH)->disabled(true);
        $edit->getButton(EditClassButton::SAVE_AND_EDIT)->disabled(true);
    }

    $edit->printEdit();
?>
<script type="text/javascript">
    function printForm(format) {
        url = api.url('frm_print.ajax.php', {'format': format});
        vars = EditClass.get().getFormData();
        api.ajax.process(ProcessType.REPORT, url, vars);
    }
</script>
