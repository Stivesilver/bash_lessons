<?php
    Security::init();

    $hisrefid   = io::geti('hisrefid');

	io::jsVar('hisrefid', $hisrefid);

    $edit = new EditClass('edit1', 0);

    $edit->title = 'Add New Document';

    $edit->addGroup('Form Information');
    $edit->addControl("Form:", "select")
        ->name('frefid')
        ->sql("
            SELECT frefid,
                   cname || ' / ' || fname
              FROM webset.disdef_fif_forms f
                   LEFT OUTER JOIN webset.disdef_fif_form_category c ON f.fcrefid = c.fcrefid
             WHERE f.vndrefid = VNDREFID
                   AND (f.enddate IS NULL OR NOW() < f.enddate)
             ORDER BY cname, f.seqnum, fname

        ")
        ->onChange('editForm()')
        ->emptyOption(true);

    $edit->addButton('Edit Form')
        ->css('width', '120px')
        ->onClick('editForm()');

    $edit->cancelURL = CoreUtils::getURL('form_list.php', array('hisrefid'=>$hisrefid));

	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();

    $edit->saveAndAdd = false;
    $edit->topButtons = false;
    $edit->printEdit();
?>
<script type="text/javascript">
    function editForm(RefID) {
	    var win = api.window.open('Edit Form',
		    api.url('./form_xml.ajax.php'),
		    {
			    'refid' : 0,
			    'hisrefid': hisrefid,
			    'frefid' : $('#frefid').val()
		    }
	    );
	    win.addEventListener('form_saved', onEvent);
	    win.maximize();
	    win.show();
    }

    function onEvent(e) {
        var edit1 = EditClass.get();
		edit1.cancelEdit();
    }
</script>
