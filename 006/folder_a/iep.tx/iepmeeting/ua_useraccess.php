<?php

    Security::init();
    $dskey = io::get('dskey');
    io::js('api.goto("' . CoreUtils::getURL('/apps/idea/iep/useraccess/ua_useraccess.php', array('dskey' => $dskey)) . '")');
?>
