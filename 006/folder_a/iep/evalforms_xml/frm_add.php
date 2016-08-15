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
                         FROM webset.statedef_forms_xml forms
                        WHERE forms.form_purpose = purpose.mfcprefid
                          AND screfid = " . VNDState::factory()->id . "
                          AND (end_date IS NULL or now()< end_date)
                          AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
                          AND NOT EXISTS (SELECT 1
                                            FROM webset.disdef_exceptions
                                           WHERE vndrefid = VNDREFID
                                             AND ex_area = 'doc_xml'
                                             AND frefid = statedef_id)
                      )
         " . (io::exists('purpose') ? "AND purpose.mfcprefid in (" . io::get('purpose') . ")" : '') . "
         ORDER BY mfcpdesc
    ";
	$edit->addControl('Purpose:', 'select_radio')
		->name('purpose')
		->sql($SQL)
		->value(db::execSQL($SQL)->getOne())
		->breakRow();

	$edit->addGroup('Form Information');
	$edit->addControl("Form:", "select_radio")
		->name('frefid')
		->sql("
            SELECT frefid,
                   form_name
              FROM webset.statedef_forms_xml forms
             WHERE forms.form_purpose = VALUE_01
               AND screfid = " . VNDState::factory()->id . "
               AND (end_date IS NULL or now() < end_date)
               AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') LIKE '%" . SystemCore::$VndName . "%'
               AND NOT EXISTS (SELECT 1
                                 FROM webset.disdef_exceptions
                                WHERE vndrefid = VNDREFID
                                  AND ex_area = 'doc_xml'
                                  AND frefid = statedef_id)
             ORDER BY form_name
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

	$edit->addControl('Cancel Url', 'hidden')
		->name('cancel_url')
		->value(CoreUtils::getVirtualPath('./frm_add.php'));

	$edit->addControl('Finish Url', 'hidden')
		->name('finish_url')
		->value(CoreUtils::getVirtualPath('./frm_main.php'));

	$edit->cancelURL = CoreUtils::getURL('frm_main.php', array('dskey' => $dskey));

	$edit->saveAndAdd = false;
	$edit->finishURL = false;

	$edit->printEdit();
?>
<script type="text/javascript">
	function editForm() {
		if ($('#frefid').val() > 0) {
			dskey = $('#dskey').val();
			frefid = $('#frefid').val();
			api.goto(api.url('./frm_xml.ajax.php'),
				{
					'dskey': dskey,
					'stateform' : frefid,
					'cancel_url' : $('#cancel_url').val(),
					'add' : 1,
					'finish_url' : $('#finish_url').val(),
					'std_id' : -1
				}
			);
		}
	}
</script>
