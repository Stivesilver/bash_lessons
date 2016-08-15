<?php

    Security::init();
    io::js('api.goto("' . CoreUtils::getURL('/apps/idea/iep/bgb/bgb_main.php', array('dskey' => io::get('dskey'), 'ESY' => io::get('ESY'))) . '")');
?>
