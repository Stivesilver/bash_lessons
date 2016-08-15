<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('dskey'=>$dskey, 'constr'=>'37')).'")');
?>
