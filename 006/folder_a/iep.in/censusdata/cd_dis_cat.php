<?php
    Security::init();
    $dskey      = io::get('dskey');
    io::js('api.goto("'.CoreUtils::getURL('/apps/idea/iep/censusdata/cd_dis_cat.php', array('dskey'=>$dskey, 'iepmode'=>'no')).'")');    
?>
