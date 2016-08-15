<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('iep_participants.php', array('dskey'=>$dskey, 'iepmode'=>'no')).'")');    
?>
