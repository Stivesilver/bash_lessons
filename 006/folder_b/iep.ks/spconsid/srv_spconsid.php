<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('/apps/idea/iep.mo/spconsid/srv_spconsid.php', array('dskey'=>$dskey, 'iepmode'=>'no')).'")');
?>
