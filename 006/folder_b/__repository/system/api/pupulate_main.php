<?php
    Security::init();
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $item = unserialize($ds->get('item'));

    $list = new ListClass();

    if (isset($item['title'])) $list->title = $item['title'];
    if (isset($item['SQL'])) $list->SQL = $item['SQL'];

    $columns = json_decode($ds->get('columns'));
    if (isset($item['searches'])) {
        foreach ($item['searches'] as $search) {
            $list->showSearchFields = true;
            $list->addSearchField($search['title'], $search['sqlField'], $search['type']);
        }
    }
    
    foreach ($item['columns'] as $column) {
        $list->addColumn($column['title'], $column['width'], $column['type']);
    }

    $list->addRecordsProcess($ds->get('caption') == '' ? 'Populate' : $ds->get('caption'))
        ->url(CoreUtils::getURL('populate_process.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->leftIcon('wizard2_16.png')
        ->onProcessDone('close_n_reload');

    $list->printList();
?>
<script type="text/javascript">
    function close_n_reload() {
        api.window.dispatchEvent("entries_populated");
        api.window.destroy();
    }
</script>
