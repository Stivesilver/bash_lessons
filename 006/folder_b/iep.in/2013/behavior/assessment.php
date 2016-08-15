<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('items.php', array('dskey'=>$dskey, 'mode'=>'F')).'")');    
?>
