<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $stdrefid = $ds->safeGet('stdrefid');

    $dskeyControl = FFInput::factory()->name('dskey')->value($dskey)->hide();

    $list = new ListClass();

    $list->title = 'IEP Documentation';

    $list->SQL = "
        SELECT smfcrefid,
               forms.smfcdate,
               mfcdoctitle,
               TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || COALESCE(' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY'),''),
               archived,
               CASE
                    WHEN xml_content IS NOT NULL THEN 'xml'
                    ELSE 'pdf'
               END
          FROM webset.std_forms forms
               INNER JOIN webset.sys_teacherstudentassignment ts ON forms.stdrefid = ts.tsrefid
               INNER JOIN webset.statedef_forms state ON forms.mfcrefid = state.mfcrefid
               INNER JOIN webset.def_formpurpose purpose ON state.mfcprefid = purpose.mfcprefid
               LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = forms.iepyear
         WHERE ts.stdrefid = " . $stdrefid . "
           " . (IDEACore::disParam(50) == 'Y' ? "AND iepyear = " . $stdIEPYear : "") . "
           " . (io::get('purpose') != '' ? "AND  state.mfcprefid in (" . io::get('purpose') . ")" : "") . "
         UNION
        SELECT smfcrefid,
               forms.smfcdate,
               uploaded_name,
               TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || COALESCE(' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY'),''),
               archived,
               'Uploaded Document'
          FROM webset.std_forms forms
               INNER JOIN webset.sys_teacherstudentassignment ts ON forms.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.std_iep_year years ON years.siymrefid = forms.iepyear
         WHERE ts.stdrefid = " . $stdrefid . "
           AND uploaded_file IS NOT NULL
           " . (IDEACore::disParam(50) == 'Y' ? "AND iepyear = " . $stdIEPYear : "") . "
           " . (io::get('purpose') != '' ? "AND  uploaded_purpose in (" . io::get('purpose') . ")" : "") . "
         ORDER BY 2 DESC, smfcrefid
    ";

    $list->addColumn('Date')->type('date');
    $list->addColumn('Title');
    $list->addColumn('IEP Year');
    $list->addColumn('Archived');

    $list->multipleEdit = false;

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_forms')
            ->setKeyField('smfcrefid')
            ->applyListClassMode()
    );

    $list->addRecordsProcess('Archive')
        ->message('Do you really want to archive selected forms?')
        ->url(CoreUtils::getURL('frm_archive.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false)
        ->css('width', '80px');

    $list->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected forms?')
        ->url(CoreUtils::getURL('frm_delete.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false)
        ->css('width', '80px');

    #$list->addButton('Upload')->onClick('uploadDoc();')->css('width', '80px');

    $list->addURL = CoreUtils::getURL('frm_add.php', array('dskey' => $dskey,
            'purpose' => (io::get('purpose') ? io::get('purpose') : null))
    );
    $list->editURL = "javascript:editForm('AF_REFID', 'AF_COL3', 'AF_COL5');";

    $list->printList();
	print $dskeyControl->toHTML();
?>
<script type="text/javascript">

    function go_to_list() {
        location = location;
    }

	function editForm(refid, format) {
		api.goto(api.url('frm_xml.php', {'RefID': refid, 'dskey': $('#dskey').val()}));
	}

</script>
