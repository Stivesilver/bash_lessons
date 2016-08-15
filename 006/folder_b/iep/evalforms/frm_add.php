<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    
    $edit = new EditClass('edit1', 0);

    $edit->title = 'Add New Document';

    $edit->addGroup('General Information');
    $SQL = "
	   	SELECT purpose.mfcprefid, 
               mfcpdesc
          FROM webset.def_formpurpose purpose
         WHERE EXISTS (SELECT 1
                         FROM webset.statedef_forms forms
                              LEFT OUTER JOIN webset.disdef_exceptions ON mfcrefid = statedef_id
                                                                       AND ex_area  = 'orderpdf'
                                                                       AND vndrefid = VNDREFID
                             WHERE COALESCE(ownpurpose, forms.mfcprefid) =  purpose.mfcprefid
                               AND screfid=" . VNDState::factory()->id . "
                               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                               AND COALESCE(onlythisip,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
                               AND NOT EXISTS (SELECT 1
                                                 FROM webset.disdef_exceptions
                                                WHERE vndrefid = VNDREFID
                                                  AND ex_area = 'document'
                                                  AND mfcrefid = statedef_id)
						      " . (io::get('purpose') != '' ? "AND forms.mfcprefid in (" . io::get('purpose') . ")" : "") . "
                       )
         ORDER BY mfcpdesc
    ";
    $edit->addControl('Purpose:', 'select_radio')
        ->name('purpose')
        ->sql($SQL)
        ->value(db::execSQL($SQL)->getOne())
        ->breakRow();

    $edit->addGroup('Form Information');
    $edit->addControl("Form:", "select_radio")
        ->name('mfcrefid')
        ->sql("
            SELECT mfcrefid, 
                   mfcdoctitle
              FROM webset.statedef_forms forms
                   LEFT OUTER JOIN webset.disdef_exceptions ON mfcrefid = statedef_id
                                                                          AND ex_area  = 'orderpdf'
                                                                          AND vndrefid = VNDREFID
             WHERE COALESCE(ownpurpose, forms.mfcprefid) = VALUE_01
               AND screfid=" . VNDState::factory()->id . "
               AND mfcfilename IS NOT NULL
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
               AND COALESCE(onlythisip,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
               AND NOT EXISTS (SELECT 1
                                 FROM webset.disdef_exceptions
                                WHERE vndrefid = VNDREFID
                                  AND ex_area = 'document'
                                  AND mfcrefid = statedef_id)            
             ORDER BY mfcdoctitle
        ")
        ->onChange('editForm()')
        ->tie('purpose')
        ->breakRow();

    $edit->addButton('Edit Form')
        ->css('width', '120px')
        ->onClick('editForm()');

    $edit->addControl('Data Storadge Key', 'hidden')
        ->name('dskey')
        ->value($dskey);

    $edit->cancelURL = CoreUtils::getURL('frm_main.php', array('dskey' => $dskey));

    $edit->saveAndAdd = false;
    $edit->topButtons = true;

    $edit->printEdit();
?>
<script type="text/javascript">
        function editForm() {
			url = api.url('frm_xml.php');
			url = api.url(url, {'RefID': 0});
			url = api.url(url, {'dskey': $('#dskey').val()});
			url = api.url(url, {'mfcrefid': $('#mfcrefid').val()});
			api.goto(url);
        }
        function go_to_list() {
            var edit1 = EditClass.get();
            edit1.cancelEdit();
        }
</script>
