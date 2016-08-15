<?php
	Security::init(NO_OUPUT);
    $ids = io::post('ids', true);
    $dskey = DataStorage::factory()->set('ids', $ids)->getKey();
    io::ajax('dskey', $dskey);
?>