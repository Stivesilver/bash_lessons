<?php

    Security::init(MODE_WS | NO_OUTPUT, 1);

    $cont = SystemCore::$physicalRoot . '/applications/webset/integration/manhattan/am.csv';

    print file_get_contents($cont);
?>