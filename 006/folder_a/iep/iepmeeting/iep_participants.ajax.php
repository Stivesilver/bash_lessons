<?php

    Security::init(NO_OUPUT);

    io::ajax('content', base64_encode(file_get_contents(CoreUtils::getPhysicalPath(io::post('url', TRUE)))));
?>