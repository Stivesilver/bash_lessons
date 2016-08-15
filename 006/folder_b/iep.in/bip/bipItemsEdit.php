<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('../fbp/items.php', array('dskey'=>$dskey, 'mode'=>'B')).'")');    
?>
