<?php
    Security::init();
    
    $edit = new EditClass('edit1', 0);

    $edit->title = 'Items Bank';
    
    $edit->addGroup("General Information");
    $edit->addControl("Items", "select_check")
        ->name('itemsbank')
        ->sql("
            SELECT ' ' || trim(ibmntdesc),
                   ibmntdesc
              FROM webset.disdef_bgb_itemsbank bank
                   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON scope.gdsrefid = bank.scoperefid
                   INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON ksa.gdsrefid = scope.gdsrefid 
                   INNER JOIN webset.std_bgb_baseline std ON std.blksa = ksa.gdskrefid
             WHERE std.blrefid = ".io::geti('baseline_id')."
             ORDER BY 1
        ")
        ->breakRow();

    $edit->finishURL = 'javascript:insertItems();';
    $edit->cancelURL = 'javascript:api.window.destroy();';
    $edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Insert Items');

    $edit->saveAndAdd = '';
    $edit->topButtons = true;    

    $edit->printEdit();
    
?>
<script type='text/javascript'>
    function insertItems() {
        api.window.dispatchEvent('items_selected', {bgbitems: $.trim($("#itemsbank").val())});
        api.window.destroy();        
    }
</script>
