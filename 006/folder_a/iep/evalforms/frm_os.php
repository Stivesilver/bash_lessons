<?php
    Security::init();
    Header('Location: ' . CoreUtils::getURL('frm_main.php', array('dskey'=>io::get('dskey'))));
?>