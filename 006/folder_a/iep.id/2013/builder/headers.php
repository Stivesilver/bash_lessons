<?php
    Security::init();
    
	$edit = new EditClass('edit1', 0);
    
    
    $edit->addControl('', 'select_check')
        ->name('iepblocks')
        ->value(io::get('hdr'))
        ->sql("
            SELECT iepnum,
                   iepdesc
              FROM webset.sped_iepblocks
             WHERE iepnum in (".io::get('str').")
               AND ieptype = ".io::get('rep')."               
             ORDER BY iepseqnum
        ")
        ->breakRow();
    $edit->firstCellWidth  = '0%';
    
    $edit->finishURL = 'javascript:assignHeaders()'; 
    $edit->cancelURL = 'javascript:api.window.destroy()'; 
    
    $edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Continue');
    
    $edit->saveAndAdd = false;
    
    $edit->printEdit();
    
?>
<script type='text/javascript'>
    function assignHeaders() {
        api.window.dispatchEvent('headersSelected', {str: $('#iepblocks').val()!='' ? $('#iepblocks').val() + ',' : ''});
        api.window.destroy();        
    }
</script>
